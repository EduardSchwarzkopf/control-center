<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Heartbeat;

class BackupCron extends ClientCron
{
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
        // TODO: Add Logic here
    }

    private function ClientRotateBackups(string $type): void
    {
        // TODO: Add logic here
    }

    private function RotateBackups(string $type): void
    {
        // TODO: Add Logic
    }

    private function PullBackup(string $type): void
    {
        // TODO: Add Logic
    }


    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan backup:cron
     */
    protected function RunCron(): void
    {

        $client = $this->client;
        $clientOptions = $this->getClientOptions;
        $heartbeat = Heartbeat::where([['client_id', "=", $client->id], ['type', '=', $this->heartbeatType]])->first();


        if ($heartbeat) {

            $dbtimestamp = strtotime($heartbeat->created_at);
            if (time() - $dbtimestamp < $clientOptions->backup_interval * 60 * 60) // hours
            {
                return;
            }
        }

        $backupTypes = ['files', 'database'];
        foreach ($backupTypes as $type) {

            $key = 'backup_' . $type;
            $this->ClientCreateBackup($type);

            $remoteAmount = $clientOptions[$key . 'amount_remote'];
            if (is_numeric($remoteAmount)) {
                $this->ClientRotateBackups($type, $remoteAmount);
            }

            $localAmount = $clientOptions[$key . 'amount_remote'];
            if (is_numeric($localAmount)) {
                $this->PullBackup($type);
                $this->RotateBackups($type, $remoteAmount);
            }
        }
    }
}
