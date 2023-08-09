<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'delivery_id',
        'start_delivery_date',
        'complete_delivery_date',
    ];
    protected $hidden = [
        'order_id',
        'delivery_id'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
