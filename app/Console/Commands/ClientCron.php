<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\JWTService;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClientCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:cron';

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

    /**
     * Execute the console command.
     *
     * debug with: php -dxdebug.mode=debug -dxdebug.start_with_request=yes artisan client:cron
     */
    public function handle()
    {
        $clientList = Client::all();
        $jwtService = new JWTService();

        foreach ($clientList as $client) {
            $clientId = $client->id;
            $clientName = $client->name;
            $options = $client->options;

            $now = date("Y-m-d H:i:s");

            $isActive = $options->is_active;

            if ($isActive == false) {
                continue;
            }

            $payload = $options->toArray();

            // Aus den Client Options holen
            $apiUrl = config('app.client_api_url');

            $seconds = 30;
            $jwtService->expiresAfter($seconds);
            $token = $jwtService->createToken();

            // Request an den Client mit allen Daten aus dem Token
            $http = new GuzzleHttpClient();
            $res = $http->post(
                $options->url . $apiUrl,
                [
                    'headers' => ['Authorization' => 'Bearer ' . $token],
                    'form_params' => $payload
                ]
            );
            
            } catch (ClientException $e) {

                $statusCode = $e->getCode();
                $message = $e->getMessage();

                Log::error("$now - CLIENT ERROR $statusCode: On client $clientName (id: $clientId) $apiUrl - $message");
                
                // Trigger Warning
                continue;

            }

            // Write to History and alert if necessary
            $statusCode = $res->getStatusCode();

            $expectedStatusCode = 200;
            $json = json_decode($res->getBody(), true);

            if ($json == null || $json->is_valid == false) {
                // Trigger Warning
                continue;
            }
            
            $infoText = "$now - $json";

            if ($statusCode != $expectedStatusCode) {
                // Trigger Warning
                $this->error($infoText);

            } else {
                $this->info($infoText);
        }
    }
}
