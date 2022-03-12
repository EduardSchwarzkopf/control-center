<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        $defaultPassword = 'controlcenter';
        $userList = [
            [
                'name' => 'admin',
                'email' => 'support@wrkbeat.com',
                'is_admin' => true,
                'password' => bcrypt($defaultPassword)
            ],
        ];

        DB::table('users')->insert($userList);
    }
}