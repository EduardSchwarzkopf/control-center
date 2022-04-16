<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public const VALIDATION_RULES = [
        'name' => ['required', 'string'],
        'url' => ['required', 'url'],
        'status_code' => ['required', 'integer'],
        'is_active' => ['nullable', 'boolean'],
        'client_environment_id' => ['required', 'integer'],

        'options' => ['nullable', 'array'],

        'options.check_interval' => ['nullable', 'integer'],

        'options.inodes_enabled' => ['nullable', 'boolean'],
        'options.backup_files_enabled' => ['nullable', 'boolean'],
        'options.backup_database_enabled' => ['nullable', 'boolean'],
        'options.backup_custom_folder' => ['nullable', 'string'],

        'options.backup_remote_enabled' => ['nullable', 'boolean'],

        'options.backup_database_max_age' => ['nullable', 'integer'],
        'options.backup_database_amount' => ['nullable', 'integer'],
        'options.backup_files_max_age' => ['nullable', 'integer'],
        'options.backup_files_amount' => ['nullable', 'integer'],

        'options.diskspace_threshold' => ['nullable', 'integer'],
        'options.inodes_threshold' => ['nullable', 'integer'],
    ];

    protected $fillable = ['name', 'url', 'is_active', 'client_environment_id', 'status_code'];
    protected $with = ['options', 'clientEnvironment'];

    protected $attributes = [
        'is_active' => true,
        'status_code' => 200,
    ];

    public function options()
    {
        return $this->hasOne(ClientOption::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function heartbeats()
    {
        return $this->hasMany(Heartbeat::class);
    }

    public function clientEnvironment()
    {
        return $this->belongsTo(ClientEnvironment::class);
    }
}
