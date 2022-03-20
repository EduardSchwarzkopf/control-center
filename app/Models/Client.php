<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public const VALIDATION_RULES = [
        'name' => ['required', 'string'],
        'is_active' => ['nullable', 'boolean'],

        'options' => ['required', 'array'],

        'url' => ['required', 'url'],

        'options.client_environment_id' => ['nullable', 'integer'],
        'options.check_interval' => ['nullable', 'integer'],
        'options.test_retries' => ['nullable', 'integer'],
        'options.retry_interval' => ['nullable', 'integer'],

        'options.inodes_enabled' => ['nullable', 'boolean'],
        'options.backup_files_enabled' => ['nullable', 'boolean'],
        'options.backup_database_enabled' => ['nullable', 'boolean'],
        'options.diskspace_enabled' => ['nullable', 'boolean'],
        'options.email_enabled' => ['nullable', 'boolean'],
        'options.backup_notification_enabled' => ['nullable', 'boolean'],
        'options.backup_remote_enabled' => ['nullable', 'boolean'],

        'options.backup_notification_receiver' => ['nullable', 'email'],
        'options.backup_custom_folder' => ['nullable', 'string'],
        'options.backup_remote_amount' => ['nullable', 'integer'],
        'options.backup_database_max_age' => ['nullable', 'integer'],
        'options.backup_database_amount' => ['nullable', 'integer'],
        'options.backup_files_max_age' => ['nullable', 'integer'],
        'options.backup_files_amount' => ['nullable', 'integer'],

        'options.diskspace_threshold' => ['nullable', 'integer'],
        'options.indoes_threshold' => ['nullable', 'integer'],
        'options.email_receiver' => ['nullable', 'email'],
    ];

    protected $fillable = ['name'];
    protected $with = ['options'];

    protected $attributes = [
        'is_active' => true,
        'inodes_enabled' => false,
        'backup_files_enabled' => false,
        'backup_database_enabled' => false,
        'diskspace_enabled' => false,
        'email_enabled' => false,
        'backup_notification_enabled' => false,
        'backup_remote_enabled' => false
    ];

    public function options()
    {
        return $this->hasOne(ClientOption::class);
    }


    public function testHistories()
    {
        return $this->hasMany(TestHistory::class);
    }
}
