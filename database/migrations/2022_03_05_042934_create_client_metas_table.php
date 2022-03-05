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
            $table->string('url');
            $table->string('environment');
            $table->string('backup_enabled');
            $table->string('inodes_enabled');
            $table->string('diskspace_enabled');
            $table->string('email_enabled');
            $table->string('backup_notification_enabled');
            $table->string('backup_notification_receiver');
            $table->string('backup_files_enabled');
            $table->string('backup_database_enabled');
            $table->string('backup_custom_folder');
            $table->string('backup_sync_enabled');
            $table->string('backup_sync_amount');
            $table->string('backup_database_max_age');
            $table->string('backup_database_amount');
            $table->string('backup_files_max_age');
            $table->string('backup_files_amount');
            $table->string('diskspace_warn_level');
            $table->string('inodes_warn_level');
            $table->string('email_receiver');
            $table->timestamps();
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
