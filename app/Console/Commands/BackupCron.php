<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupCron extends ClientCron
{
    private string $clientBackupUrl;
    private array $clientBackupList;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron for backups';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function HeartbeatType(): string
    {
        return 'backup_cron';
    }

    protected function GetClientList()
    {
        return Client::where('is_active', "=", true)
            ->with(["options" => function ($q) {
                $q->orWhere('backup_files_enabled', '=', true)
                    ->orWhere('backup_database_enabled', '=', true);
            }])->get();
    }

    private function ClientCreateBackup(string $type): void
    {

        $clientEnvironment = $this->client->clientEnvironment;
        $envName = $clientEnvironment->name;

        $this->clientRequest->post(
            $this->clientBackupUrl,
            [
                'platform' => $envName,
                $type => true,
            ]
        );
    }

    private function GetClientBackupList(): array
    {

        $all = 'all';
        $clientBackupResponse = $this->clientRequest->get(
            $this->clientBackupUrl,
            ['files' => $all, 'database' => $all]
        );

        $responseList = json_decode($clientBackupResponse->getBody(), true);

        $this->clientBackupList = $responseList['data'];

        return $this->clientBackupList;
    }

    private function ClientRotateBackups(string $type, int $amount): void
    {

        $backupList = $this->clientBackupList;

        if (key_exists($type, $backupList) == false) {
            return;
        }

        $backupTypeList = $backupList[$type];

        $backupCount = count($backupTypeList);

        if ($amount > $backupCount) {
            // Not enough backups, abort
            return;
        }



        // update local list
        $this->clientBackupList[$type] = $backupTypeList;
    }

    private function RotateBackups(string $type, int $amount): void
    {

        $clientBackupPath = 'backups/' . $this->clientId . '/' . $type;

        $path = storage_path('app') . '/' . $clientBackupPath;
        $files = glob($path . '/*.*');
        $localFileCount = count($files);
        if ($amount >= $localFileCount) {
            return;
        }

        $slicedList = array_slice($files, 0, $localFileCount - $amount);

        foreach ($slicedList as $filePath) {
            unlink($filePath);
        }
    }

    private function PullBackup(string $type): void
    {

        $backupList = $this->clientBackupList;
        if (key_exists($type, $backupList) == false) {
            return;
        }

        $storage = Storage::disk('local');
        $backupTypeList = $backupList[$type];

        // We want the second last backup, because the current one might still be in creation
        end($backupTypeList);
        $latestBackup = prev($backupTypeList);
        $latestBackupName = $latestBackup['name'];
        $clientBackupPath = 'backups/' . $this->clientId . '/' . $type . '/' . $latestBackupName;

        if ($storage->exists($clientBackupPath)) {
            return;
        }

        $url = $this->clientBackupUrl . '/' . $latestBackupName;
        $response = $this->clientRequest->get($url, []);
        $file = $response->getBody()->getContents();

        $isSaved = $storage->put($clientBackupPath, $file);

        if ($isSaved == false) {

            $message = "Backup file: $latestBackupName\ntype: $type";

            $this->TriggerWarning('File not saved', $message);
            Log::error($message);
        }
    }


    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan backup:cron
     */
    protected function RunCron(): void
    {

        $client = $this->client;
        $clientOptions = $this->clientOptions;
        $heartbeat = Heartbeat::where([['client_id', "=", $client->id], ['type', '=', $this->heartbeatType]])->first();
        $heartbeat = null;
        $this->clientBackupUrl = $this->clientApiUrl . '/backups';

        if ($heartbeat) {

            $dbtimestamp = strtotime($heartbeat->created_at);
            if (time() - $dbtimestamp < $clientOptions->backup_interval * 60 * 60) // hours
            {
                return;
            }
        }

        $this->clientBackupList = $this->GetClientBackupList();

        $backupTypes = ['files', 'database'];
        foreach ($backupTypes as $type) {

            $key = 'backup_' . $type;
            $this->ClientCreateBackup($type);

            $remoteAmount = $clientOptions[$key . '_amount_remote'];
            if (is_numeric($remoteAmount)) {
                $this->ClientRotateBackups($type, $remoteAmount);
            }

            $localAmount = $clientOptions[$key . '_amount_remote'];
            if (is_numeric($localAmount)) {
                $this->PullBackup($type);
                $this->RotateBackups($type, $localAmount);
            }
        }
    }
}
