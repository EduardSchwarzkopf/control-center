<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Heartbeat extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['client_id', 'type', 'status', 'message', 'value', 'created_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
