<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('client_environments')->insert([
            ['name' => 'None'],
            ['name' => 'Wordpress'],
            ['name' => 'Magento 1'],
            ['name' => 'Magento 2']
        ]);
    }
}