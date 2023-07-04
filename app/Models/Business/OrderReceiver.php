<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderReceiver extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'receiver_name', 'receiver_phone', 'note'
    ];
    protected $hidden = [
        'order_id'
    ];
}
