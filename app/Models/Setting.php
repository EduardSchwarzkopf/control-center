<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public const VALIDATION_RULES = [
        'name' => ['required', 'string'],
        'label' => ['required', 'string'],
        'value' => ['required', 'string'],
        'type' => ['required', 'string'],

    ];

    protected $fillable = ['name', 'label', 'value', 'type'];
}
