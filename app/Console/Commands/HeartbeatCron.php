<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientOption;
use App\Models\Heartbeat;
use App\Services\ClientApiRequest;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HeartbeatCron extends Command
{
    private int $clientId = 0;
    private string $clientName = '';

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

    private function TriggerWarning(string $title, string $message): void
    {

        $to = env('ALERT_RECEIVER');
        $clientId = $this->clientId;
        $clientName = $this->clientName;
        $subject = "CC-WARNING: $clientName ($clientId) - $title";
        $message = $message;

        $headers = 'From: ' . env('MAIL_USERNAME') . "\r\n";
        $headers .= "Content-type: text/html\r\n";

        $result = mail($to, $subject, $message, $headers);

        if ($result == false) {
            Log::error('MAIL ERROR: Could not send email with title: ' . $title);
        }
    }

    private function CreateHeartbeat(string $type, bool $status, string $message, $value = null): void
    {
        Heartbeat::create([
            'client_id' => $this->clientId,
            'type' => $type,
            'status' => $status,
            'value' => $value,
            'message' => $message,
        ]);
    }

    private function CheckStatusCode(GuzzleHttpClient $request, string $url): int
    {

        try {
            $res = $request->get($url);
            $statusCode = $res->getStatusCode();
        } catch (GuzzleException $e) {

            $statusCode = $e->getCode();
        }

        return $statusCode;
    }

    private function CheckSystem(ClientOption $clientOptions, ClientApiRequest $clientRequest, $url): void
    {

        $systemQueryList = [];
        $diskUsageThreshold = $clientOptions->diskspace_threshold;
        if (is_numeric($diskUsageThreshold) && $diskUsageThreshold > 0) {
            $systemQueryList['diskusage'] = $diskUsageThreshold;
        }

        $inodesThreshold = $clientOptions->inodes_threshold;
        if (is_numeric($inodesThreshold) && $inodesThreshold > 0) {
            $systemQueryList['inodes'] = $inodesThreshold;
        }

        $responseList = $this->GetApiResponse($clientRequest, $url, $systemQueryList);

        if ($responseList == null) {
            $this->TriggerWarning(
                'No Response',
                'No Response on url: ' - $url
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

    private function CheckBackups(ClientOption $clientOptions, ClientApiRequest $clientRequest, $url): void
    {

        $backupQueryParameterList = [];
        $databaseMaxHours = $clientOptions->backup_database_max_age;
        if (is_numeric($databaseMaxHours) && $databaseMaxHours > 0) {
            $backupQueryParameterList['file'] = $databaseMaxHours;
        }

        $fileMaxHours = $clientOptions->backup_database_max_age;
        if (is_numeric($fileMaxHours) && $fileMaxHours > 0) {
            $backupQueryParameterList['database'] = $fileMaxHours;
        }

        $responseList = $this->GetApiResponse($clientRequest, $url, $backupQueryParameterList);

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

    private function GetApiResponse(ClientApiRequest $clientRequest, string $url, array $queryParameterList): array
    {
        $clientResponse = $clientRequest->get($url, $queryParameterList);


        if ($clientResponse == null) {
            $this->TriggerWarning(
                'NO RESPONSE',
                "No response from client at $url"
            );
            return [];
        }

        $responseList = json_decode($clientResponse->getBody(), true);


        if ($responseList == null || count($responseList) == 0) {
            $this->TriggerWarning(
                'NO DATA RECEIVED',
                'No data reveived from client at ' . $url
            );
            return [];
        }

        return $responseList;
    }

    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan heartbeat:cron
     */
    public function handle()
    {
        $clientList = Client::all();
        $request = new GuzzleHttpClient();
        $clientRequest = new ClientApiRequest();

        foreach ($clientList as $client) {
            $this->clientId = $client->id;
            $this->clientName = $client->name;

            $clientEnvironment = $client->clientEnvironment;
            $envName = $clientEnvironment->name;
            $clientOptions = $client->options;

            $isActive = $client->is_active;
            if ($isActive == false) {
                continue;
            }

            // Time to check again?
            $heartbeat = Heartbeat::where('client_id', "=", $client->id)->first();

            if ($heartbeat) {

                $dbtimestamp = strtotime($heartbeat->created_at);
                if (time() - $dbtimestamp < $clientOptions->check_interval) {
                    continue;
                }
            }


            $apiUrl = config('app.client_api_url');
            $clientUrl = $client->url;

            $checkStatusCode = $this->CheckStatusCode($request, $clientUrl);
            $expectedStatusCode = $client->status_code ? $client->status_code : 200;
            $type = 'status_code';
            $checkStatusMessage = "Expected: $expectedStatusCode ; Expected: $checkStatusCode";
            $this->CreateHeartbeat($type, $expectedStatusCode == $checkStatusCode, $checkStatusCode, $checkStatusMessage);

            if ($checkStatusCode != $expectedStatusCode) {
                $this->TriggerWarning(
                    'STATUS CODE: ' . $checkStatusCode,
                    'Status code error: ' . $checkStatusMessage
                );
            }

            $baseUrl = $clientUrl . $apiUrl;
            $this->CheckBackups($clientOptions, $clientRequest, $baseUrl . '/backups');
            $this->CheckSystem($clientOptions, $clientRequest, $baseUrl . '/system');
        }
    }
}
