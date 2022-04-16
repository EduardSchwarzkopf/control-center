<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOption extends Model
{
    use HasFactory;

    // DOKU: Backup interval für Database und Files in eigene Logik
    protected $fillable = [
        'client_id',
        'check_interval',
        'backup_interval',
        'backup_database_enabled',
        'backup_database_max_age',
        'backup_database_amount',
        'backup_database_amount_remote',
        'backup_files_enabled',
        'backup_files_max_age',
        'backup_files_amount',
        'backup_files_amount_remote',
        'diskspace_threshold',
        'inodes_threshold',
    ];

    protected $attributes = [
        'check_interval' => 900,
        'backup_files_enabled' => false,
        'backup_database_enabled' => false,
    ];

    protected $casts = [
        'backup_database_enabled' => 'boolean',
        'backup_files_enabled' => 'boolean',
    ];


    public function getClientPayload(): array
    {

        $requestData = [
            'backup_files_enabled' => false,
            'backup_database_enabled' => false,
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
