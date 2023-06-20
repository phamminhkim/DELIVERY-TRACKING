<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'delivery_address', 'note', 'total_item', 'total_weight', 'total_value'
    ];
    protected $hidden = [
        'order_id'
    ];
}
