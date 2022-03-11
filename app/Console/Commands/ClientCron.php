<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientOption;
use App\Services\JWTService;
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
     * @return int
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

            $jwtService->expiresAfter(5);
            $token = $jwtService->createToken($payload);

            // Request an den Client mit allen Daten aus dem Token

            $infoText = "$token";

            Log::info($infoText);
            $this->info($infoText);
        }
    }
}
