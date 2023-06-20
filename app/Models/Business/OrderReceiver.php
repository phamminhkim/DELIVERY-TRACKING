<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderReceiver extends Model
{
    protected $fillable = [
        'receiver_name', 'receiver_phone'
    ];
    protected $hidden = [
        'order_id'
    ];
}
