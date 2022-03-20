<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'check_interval',
        'test_retries',
        'retry_interval',
        'inodes_enabled',
        'diskspace_enabled',
        'email_enabled',
        'backup_notification_enabled',
        'backup_notification_receiver',
        'backup_files_enabled',
        'backup_database_enabled',
        'backup_custom_folder',
        'backup_remote_enabled',
        'remote_backup_amount',
        'backup_database_max_age',
        'backup_database_amount',
        'backup_files_max_age',
        'backup_files_amount',
        'diskspace_threshold',
        'indoes_threshold',
        'email_receiver'
    ];

    protected $attributes = [
        'check_interval' => 900,
        'test_retries' => 0,
        'retry_interval' => 900,

        'inodes_enabled' => false,
        'backup_files_enabled' => false,
        'backup_database_enabled' => false,
        'diskspace_enabled' => false,
        'email_enabled' => false,
        'backup_notification_enabled' => false,
        'backup_remote_enabled' => false
    ];


    public function getClientPayload(): array
    {

        $requestData = [
            'inodes_enabled' => false,
            'backup_files_enabled' => false,
            'backup_database_enabled' => false,
            'diskspace_enabled' => false,
            'email_enabled' => false,
            'backup_notification_enabled' => false,
            'backup_remote_enabled' => false,
        ];

        foreach ($requestData as $key => $value) {

            $requestData[$key] = boolval($this[$key]);
        }

        $requestData['environment'] = $this['clientEnvironment']->name;

        return $requestData;
    }


    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
