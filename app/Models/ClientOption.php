<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'is_active',
        'check_interval',
        'test_retries',
        'retry_interval',
        'url',
        'inodes_enabled',
        'diskspace_enabled',
        'email_enabled',
        'backup_notification_enabled',
        'backup_notification_receiver',
        'backup_files_enabled',
        'backup_database_enabled',
        'backup_custom_folder',
        'remote_backup_enabled',
        'remote_backup_amount',
        'backup_database_max_age',
        'backup_database_amount',
        'backup_files_max_age',
        'backup_files_amount',
        'diskspace_warn_level',
        'inodes_warn_level',
        'email_receiver'
    ];
    
     protected $attributes = [
        'is_active' => true,
        'check_interval' => 900,
        'test_retries' => 0,
        'retry_interval' => 900,
        'client_environment_id' => 1,

        'inodes_enabled' => false,
        'backup_files_enabled' => false,
        'backup_database_enabled' => false,
        'diskspace_enabled' => false,
        'email_enabled' => false,
        'backup_notification_enabled' => false,
        'remote_backup_enabled' => false
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function environment()
    {
        return $this->belongsTo(ClientEnvironment::class);
    }
}
