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
                'name' => 'monitor_history',
                'value' => '180',
            ], [
                'name' => 'backup_max_allowed_size',
                'value' => '50',
            ], [
                'name' => 'remote_backups_location',
                'value' => './client-backups',
            ]
        ];

        DB::table('settings')->insert($settingsList);
    }
}