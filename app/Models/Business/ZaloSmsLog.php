<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ZaloSmsLog extends Model
{
    protected $fillable = [
        'customer_id',
        'phone',
        'template_id',
        'template_data',
        'is_success',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'is_success' => 'boolean',
    ];
}
