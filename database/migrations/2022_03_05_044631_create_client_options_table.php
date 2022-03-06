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

            $table->unsignedBigInteger('client_environment_id')->nullable();
            $table->foreign('client_environment_id')->references('id')->on('client_environments');

            $table->boolean('is_active');
            $table->integer('check_interval');
            $table->integer('test_retries');
            $table->integer('retry_interval');

            $table->string('url');
            $table->boolean('inodes_enabled');
            $table->boolean('backup_files_enabled');
            $table->boolean('backup_database_enabled');
            $table->boolean('diskspace_enabled');
            $table->boolean('email_enabled');
            $table->boolean('backup_notification_enabled');
            $table->boolean('backup_remote_enabled');

            $table->string('backup_notification_receiver')->nullable();
            $table->string('backup_custom_folder')->nullable();
            $table->integer('backup_remote_amount')->nullable();
            $table->integer('backup_database_max_age')->nullable();
            $table->integer('backup_database_amount')->nullable();
            $table->integer('backup_files_max_age')->nullable();
            $table->integer('backup_files_amount')->nullable();

            $table->float('diskspace_warn_level')->nullable();
            $table->float('inodes_warn_level')->nullable();
            $table->string('email_receiver')->nullable();
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
