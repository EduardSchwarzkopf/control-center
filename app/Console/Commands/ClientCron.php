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
        $token = $jwtService->token;


        foreach($clientList as $client) {
            $id = $client->id;
            $clientName = $client->name;
            $options = ClientOption::where('client_id', "=", $id)->first();

            $infoText = "ClientCron: $clientName ($id)";

            Log::info($infoText);
            $this->info($infoText);
        }
    }
}
