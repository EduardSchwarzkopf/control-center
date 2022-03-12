<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        $settingsList = [
            [
                'name' => 'client_api_url',
                'label' => 'Client API URL',
                'value' => '/wrkbeat-client/api.php',
            ], [
                'name' => 'monitor_history',
                'label' => 'Monitor history',
                'value' => '180',
            ], [
                'name' => 'backup_max_allowed_size',
                'label' => 'Backup maximum file size ',
                'value' => '50',
            ], [
                'name' => 'remote_backups_location',
                'label' => 'Remote backup storage folder',
                'value' => './client-backups',
            ]
        ];

        DB::table('settings')->insert($settingsList);
    }
}