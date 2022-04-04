<?php

namespace App\Console\Commands;

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

    protected function RunCron(): void
    {
    }


    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan backup:cron
     */
    public function handle()
    {

        $client = $this->client;
        $heartbeat = Heartbeat::where([['client_id', "=", $client->id], ['type', '=', $this->heartbeatType]])->first();

        if ($heartbeat) {

            $dbtimestamp = strtotime($heartbeat->created_at);
            if (time() - $dbtimestamp < $this->clientOptions->check_interval) {
                return;
            }
        }

        // TODO: Add to Client model: backup_database_interval, backup_file_interval
        // TODO: Create Backup
        // TODO: Pull Backup
        // TODO: Rotate Backup
        // TODO: Rotate Backup on Client
    }
}
