<?php

use Database\Seeders\ClientEnvironmentSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_environments', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique();
            $table->string('name');
        });

        $seeder = new ClientEnvironmentSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_environments');
    }
};
