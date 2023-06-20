<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPartner extends Model
{
    protected $fillable = [
        'code',
        'name',
        'api_url',
        'api_key',
        'api_secret',
        'is_external',
        'is_active',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_active' => 'boolean'
    ];
}
