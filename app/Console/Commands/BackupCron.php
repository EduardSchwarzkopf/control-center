<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Heartbeat;
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

        $toRemove = $backupCount - $amount;
        for ($i = 0; $i < $toRemove; $i++) {
            $backupFile = $backupTypeList[$i];

            $backupUrl = $this->clientBackupUrl . '/' . $backupFile['name'];
            $this->clientRequest->delete($backupUrl);

            unset($backupTypeList[$i]);
        }

        // update local list
        $this->clientBackupList[$type] = $backupTypeList;
    }

    private function RotateBackups(string $type): void
    {
        // TODO: Add Logic

        // TODO: place in: storage/backups/client_id/type/file.ext
        // Check if already exist
    }

    private function PullBackup(string $type): void
    {
        // TODO: Add Logic

        $backupList = $this->clientBackupList;
        if (key_exists($type, $backupList) == false) {
            return;
        }

        $latestBackup = end($backupList[$type]);


        // TODO: WIP
        $backupPath = 'backups/' . $this->clientId . '/' . $latestBackup;
        $filePath = storage_path('app') . $backupPath;
        if (file_exists($filePath)) {
            return;
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
