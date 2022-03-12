<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientOption;
use App\Models\Settings;
use App\Services\JWTService;
use GuzzleHttp\Client as GuzzleHttpClient;
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

        foreach($clientList as $client) {
            $clientId = $client->id;
            $clientName = $client->name;
            $options = ClientOption::where('client_id', "=", $clientId)->first();

            $isActive = $options->is_active;

            if ($isActive == false) {
                continue;
            }

            $payload = $options->toArray();

            // Aus den Client Options holen
            $apiUrl = $ccSettings->value;

            $seconds = 30;
            $jwtService->expiresAfter($seconds);
            $token = $jwtService->createToken();

            // Request an den Client mit allen Daten aus dem Token
            $http = new GuzzleHttpClient();
            $res = $http->post(
                $options->url . $apiUrl,
            [
                'headers' => [ 'Authorization' => 'Bearer ' . $token ],
                'form_params' => $payload
            ]);
            $body = $res->getBody();
            $infoText = "$body";

            Log::info($infoText);
            $this->info($infoText);
        }
    }
}
