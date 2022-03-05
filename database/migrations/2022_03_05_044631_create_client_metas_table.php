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
        Schema::create('client_metas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            // Relationships
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unsignedBigInteger('client_environment_id');
            $table->foreign('client_environment_id')->references('id')->on('client_environments');

            $table->string('url')->unique();
            $table->boolean('backup_enabled');
            $table->boolean('inodes_enabled');
            $table->boolean('diskspace_enabled');
            $table->boolean('email_enabled');
            $table->boolean('backup_notification_enabled');
            $table->string('backup_notification_receiver');
            $table->boolean('backup_files_enabled');
            $table->boolean('backup_database_enabled');
            $table->string('backup_custom_folder');
            $table->boolean('backup_sync_enabled');
            $table->integer('backup_sync_amount');
            $table->integer('backup_database_max_age');
            $table->integer('backup_database_amount');
            $table->integer('backup_files_max_age');
            $table->integer('backup_files_amount');
            $table->float('diskspace_warn_level');
            $table->float('inodes_warn_level');
            $table->string('email_receiver');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_metas');
    }
};
