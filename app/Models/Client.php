<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

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
