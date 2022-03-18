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
            ['label' => 'None', 'name' => 'none'],
            ['label' => 'Wordpress', 'name' => 'wordpress'],
            ['label' => 'Magento 1', 'name' => 'magento_1'],
            ['label' => 'Magento 2', 'name' => 'magento_2']
        ]);
    }
}