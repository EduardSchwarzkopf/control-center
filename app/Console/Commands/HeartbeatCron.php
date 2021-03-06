<?php

namespace App\Console\Commands;

use App\Models\Heartbeat;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $checkStatusMessage = "Expected: $expectedStatusCode ; Got: $statusCode";
        $result = $expectedStatusCode == $statusCode;

        $this->CreateHeartbeat($this->heartbeatType, $result, $statusCode, $checkStatusMessage);

        if ($result == false) {
            $this->errorList['STATUS CODE: ' . $statusCode] = 'Status code error: ' . $checkStatusMessage;
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

        if (empty($responseList)) {
            return;
        }


        foreach ($systemQueryList as $checkItem => $threshold) {

            $responseItem = $responseList['data'][$checkItem];
            $checkStatus = $responseItem < $threshold;
            $message = $checkItem . ' is now at ' . $responseItem . '%';

            if ($checkStatus == false) {
                $this->errorList['DISKUSAGE'] = $message . '. Max threshold is: ' . $threshold;
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
            $backupQueryParameterList['files'] = $databaseMaxHours;
        }

        $fileMaxHours = $clientOptions->backup_database_max_age;
        if (is_numeric($fileMaxHours) && $fileMaxHours > 0) {
            $backupQueryParameterList['database'] = $fileMaxHours;
        }

        $apiBackupUrl = $this->clientApiUrl . '/backups';
        $responseList = $this->GetApiResponse($this->clientRequest, $this->clientApiUrl . '/backups', $backupQueryParameterList);

        if (empty($responseList)) {
            return;
        }

        foreach ($backupQueryParameterList as $checkItem => $threshold) {
            $responseItem = $responseList['data'][$checkItem];

            // TODO: Trigger alert if requested a second time
            if (empty($responseItem)) {
                continue;
            }

            $ageHours = $responseItem['hours'];
            $checkStatus = $ageHours < $threshold;

            if ($checkStatus == true) {
                // Get Recent Backup from Client
                $this->PullBackup($apiBackupUrl, $responseItem['name'], $checkItem);
            } else {
                $this->errorList['Backup to old'] = "Backup age is $ageHours\nMax allowed hours: $threshold";
            }

            $this->CreateHeartbeat($checkItem, $checkStatus, $ageHours, '');
        }
    }

    private function PullBackup(string $apiBackupUrl, string $filename, string $backupType): void
    {

        $localAmount = $this->clientOptions['backup_' . $backupType . '_amount'];
        if (is_numeric($localAmount) == false && $localAmount < 1) {
            return;
        }

        $storage = Storage::disk('local');

        $clientBackupPath = 'backups/' . $this->clientId . '/' . $backupType . '/' . $filename;

        if ($storage->exists($clientBackupPath)) {
            return;
        }

        $url = $apiBackupUrl . '/' . $filename;
        $response = $this->clientRequest->get($url, []);

        if ($response == null) {
            Log::error('No Backup found on Client ' . $this->clientName);
            return;
        }

        $file = $response->getBody()->getContents();

        $isSaved = $storage->put($clientBackupPath, $file);

        if ($isSaved == false) {

            $message = "Backup file: $filename\ntype: $backupType";

            $this->errorList['File not saved'] = $message;
            Log::error($message);
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
            $timePast = Carbon::now()->timestamp - $dbtimestamp;
            if ($timePast < $clientOptions->check_interval) {
                return;
            }
        }

        $this->CheckStatusCode();
        $this->CheckBackups();
        $this->CheckSystem();
    }
}
