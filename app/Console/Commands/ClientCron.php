<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientOption;
use App\Models\Heartbeat;
use App\Services\ClientApiRequest;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\Log;

abstract class ClientCron extends Command
{
    protected Client $client;
    protected ?ClientOption $clientOptions;
    protected ClientApiRequest $clientRequest;
    protected GuzzleHttpClient $httpClient;
    protected string $apiUrl;


    protected string $clientApiUrl = '';
    protected int $clientId = 0;
    protected string $clientName = '';
    protected string $heartbeatType = '';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'backup:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Cron base for a client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->heartbeatType = $this->HeartbeatType();
        $this->clientRequest = new ClientApiRequest();
        $this->httpClient = new GuzzleHttpClient();
        $this->apiUrl = config('app.client_api_url');

        parent::__construct();
    }


    abstract protected function RunCron(): void;
    abstract protected function HeartbeatType(): string;

    protected function CreateHeartbeat(string $type, bool $status, string $message, $value = null): void
    {
        Heartbeat::create([
            'client_id' => $this->clientId,
            'type' => $type,
            'status' => $status,
            'value' => $value,
            'message' => $message,
        ]);
    }

    protected function TriggerWarning(string $title, string $message): void
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

        $this->warn($subject . ': ' . $message);
    }

    protected function GetApiResponse(ClientApiRequest $clientRequest, string $url, array $queryParameterList): array
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

    protected function GetClientList()
    {
        return Client::where('is_active', '=', true)->get();
    }


    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan backup:cron
     */
    public function handle()
    {

        $type = $this->heartbeatType;
        if (empty($type)) {
            return;
        }

        $clientList = $this->GetClientList();

        foreach ($clientList as $client) {
            $this->client = $client;
            $this->clientId = $client->id;
            $this->clientName = $client->name;
            $this->clientApiUrl = $client->url . $this->apiUrl;

            $this->clientOptions = $client->options;

            $this->RunCron();

            // TODO: Error array and trigger Warnings once
        }
    }
}
