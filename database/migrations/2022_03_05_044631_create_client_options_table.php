<?php

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
        Schema::create('client_options', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Relationships
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->integer('check_interval');

            $table->boolean('backup_database_enabled');
            $table->integer('backup_database_max_age')->nullable();
            $table->integer('backup_database_amount')->nullable();

            $table->boolean('backup_files_enabled');
            $table->integer('backup_files_max_age')->nullable();
            $table->integer('backup_files_amount')->nullable();

            $table->float('diskspace_threshold')->nullable();
            $table->float('inodes_threshold')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_options');
    }
};
