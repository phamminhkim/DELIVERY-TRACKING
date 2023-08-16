<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
class ServiceToken extends Model
{
    // public $timestamps = false;

    protected $fillable = [
        'provider',
        'category',
        'access_token',
        'refresh_token',
        'expired_at',
    ];
    protected $casts = [
        'expired_at' => 'datetime'
    ];
}

