<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public const VALIDATION_RULES = [
        'name' => ['required', 'string'],

        'options' => ['required', 'array'],

        'options.url' => ['required', 'url'],

        'options.client_environment_id' => ['nullable', 'integer'],
        'options.is_active' => ['nullable', 'boolean'],
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

        'options.diskspace_warn_level' => ['nullable', 'integer'],
        'options.inodes_warn_level' => ['nullable', 'integer'],
        'options.email_receiver' => ['nullable', 'email'],
    ];

    protected $fillable = ['name'];

    public function options()
    {
        return $this->hasOne(ClientOption::class);
    }

    public function testHistories()
    {
        return $this->hasMany(TestHistory::class);
    }
}
