<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Heartbeat;
use App\Services\ClientApiRequest;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use stdClass;

class HeartbeatCron extends Command
{
    private int $clientId = 0;
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
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function TriggerWarning(): void
    {

        // Add Magic here

    }

    private function CreateHeartbeat(string $type, bool $status, string $message): void
    {
        Heartbeat::create([
            'client_id' => $this->clientId,
            'type' => $type,
            'status' => $status,
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

            $this->TriggerWarning();
        }

        return $statusCode;
    }

    private function CheckDiskusage(stdClass $jsonResponse, $threshold): void
    {
    }

    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan client:cron
     */
    public function handle()
    {
        $clientList = Client::all();
        $request = new GuzzleHttpClient();
        $clientRequest = new ClientApiRequest();

        foreach ($clientList as $client) {
            $this->clientId = $client->id;
            $clientEnvironment = $client->clientEnvironment;
            $envName = $clientEnvironment->name;
            $clientOptions = $client->options;

            $now = date("Y-m-d H:i:s");

            // TODO: Check for check_interval

            $isActive = $client->is_active;

            if ($isActive == false) {
                continue;
            }

            $apiUrl = config('app.client_api_url');
            $clientUrl = $client->url;

            $checkStatusCode = $this->CheckStatusCode($request, $clientUrl);
            $expectedStatusCode = $client->status_code ? $client->status_code : 200;
            $type = 'status_code';
            $checkStatusMessage = "Expected: $expectedStatusCode ; Expected: $checkStatusCode";
            $this->CreateHeartbeat($type, $expectedStatusCode == $checkStatusCode, $checkStatusMessage);

            if ($checkStatusCode != $expectedStatusCode) {
                $this->TriggerWarning();
            }


            $queryParameters = [];
            if ($envName != 'None') {
                $queryParameters['platform'] = $envName;
            }

            if (is_numeric($clientOptions->backup_database_max_age)) {
                // TODO: Add to db check
            }

            if (is_numeric($clientOptions->backup_files_max_age)) {
                // TODO: Add to db check
            }

            $diskUsageThreshold = $clientOptions->diskspace_threshold;
            if (is_numeric($diskUsageThreshold) && $diskUsageThreshold > 0) {
                $queryParameters['diskusage'] = true;
            }

            $inodesThreshold = $clientOptions->inodes_threshold;
            if (is_numeric($inodesThreshold) && $inodesThreshold > 0) {
                $queryParameters['inodes'] = true;
            }

            $systemApi = $clientUrl . $apiUrl . '/system';
            $response = $clientRequest->get($systemApi, $queryParameters);

            $jsonResponse = json_decode($response->getBody(), true);

            if ($jsonResponse == null) {
                $this->TriggerWarning();
                continue;
            }

            foreach ($queryParameters as $checkItem => $value) {

                $checkStatus = null;
                switch ($checkItem) {
                    case 'diskusage':
                    case 'inodes':
                        $responseItem = $jsonResponse['data'][$checkItem];
                        $checkStatus = $responseItem < 80;
                        $message = $checkItem . ' is now at ' . $responseItem . '%';
                        break;

                    default:
                        break;
                }

                if ($checkStatus == null) {
                    continue;
                }

                $this->CreateHeartbeat($checkItem, $checkStatus, $message);
            }
        }
    }
}
