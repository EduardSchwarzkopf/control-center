<?php

namespace App\Console\Commands;

use App\Models\Heartbeat;
use GuzzleHttp\Exception\GuzzleException;

class HeartbeatCron extends ClientCron
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heartbeat:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check client health status';

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
        return 'status_code';
    }

    private function CheckStatusCode(): void
    {

        $client = $this->client;
        try {
            $res = $this->httpClient->get($client->url);
            $statusCode = $res->getStatusCode();
        } catch (GuzzleException $e) {

            $statusCode = $e->getCode();
        }

        $expectedStatusCode = $client->status_code ? $client->status_code : 200;
        $checkStatusMessage = "Expected: $expectedStatusCode ; Expected: $statusCode";
        $result = $expectedStatusCode == $statusCode;

        $this->CreateHeartbeat($this->heartbeatType, $result, $statusCode, $checkStatusMessage);

        if ($result == false) {
            $this->TriggerWarning(
                'STATUS CODE: ' . $statusCode,
                'Status code error: ' . $checkStatusMessage
            );
        }
    }

    private function CheckSystem(): void
    {

        $systemQueryList = [];
        $clientOptions = $this->clientOptions;
        $diskUsageThreshold = $clientOptions->diskspace_threshold;
        if (is_numeric($diskUsageThreshold) && $diskUsageThreshold > 0) {
            $systemQueryList['diskusage'] = $diskUsageThreshold;
        }

        $inodesThreshold = $clientOptions->inodes_threshold;
        if (is_numeric($inodesThreshold) && $inodesThreshold > 0) {
            $systemQueryList['inodes'] = $inodesThreshold;
        }

        $url = $this->clientApiUrl . '/system';
        $responseList = $this->GetApiResponse($this->clientRequest, $url, $systemQueryList);

        if ($responseList == null) {
            $this->TriggerWarning(
                'No Response',
                'No Response on url: ' . $url
            );
            return;
        }


        foreach ($systemQueryList as $checkItem => $threshold) {

            $responseItem = $responseList['data'][$checkItem];
            $checkStatus = $responseItem < $threshold;
            $message = $checkItem . ' is now at ' . $responseItem . '%';

            if ($checkStatus == false) {
                $this->TriggerWarning(
                    'DISKUSAGE',
                    $message . '. Max threshold is: ' . $threshold
                );
            }

            $this->CreateHeartbeat($checkItem, $checkStatus, $responseItem, $message);
        }
    }

    private function CheckBackups(): void
    {

        $clientOptions = $this->clientOptions;
        $backupQueryParameterList = [];
        $databaseMaxHours = $clientOptions->backup_database_max_age;
        if (is_numeric($databaseMaxHours) && $databaseMaxHours > 0) {
            $backupQueryParameterList['file'] = $databaseMaxHours;
        }

        $fileMaxHours = $clientOptions->backup_database_max_age;
        if (is_numeric($fileMaxHours) && $fileMaxHours > 0) {
            $backupQueryParameterList['database'] = $fileMaxHours;
        }

        $responseList = $this->GetApiResponse($this->clientRequest, $this->clientApiUrl, $backupQueryParameterList);

        foreach ($backupQueryParameterList as $checkItem => $threshold) {
            $responseItem = $responseList['data'][$checkItem]['hours'];
            $checkStatus = $responseItem < $threshold;

            if ($checkStatus == false) {
                $this->TriggerWarning(
                    'Backup to old',
                    "Backup age is $responseItem\n
                    Max allowed hours: $threshold"
                );
            }

            $this->CreateHeartbeat($checkItem, $checkStatus, $responseItem, '');
        }
    }

    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan heartbeat:cron
     */
    protected function RunCron(): void
    {
        $client = $this->client;
        $clientOptions = $this->clientOptions;

        // Time to check again?
        $heartbeat = Heartbeat::where([['client_id', "=", $client->id], ['type', '=', $this->heartbeatType]])->first();
        if ($heartbeat) {

            $dbtimestamp = strtotime($heartbeat->created_at);
            if (time() - $dbtimestamp < $clientOptions->check_interval) {
                return;
            }
        }

        $this->CheckStatusCode();
        $this->CheckBackups();
        $this->CheckSystem();
    }
}
