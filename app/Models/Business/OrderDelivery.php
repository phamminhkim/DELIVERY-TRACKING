<?php

namespace App\Models\Business;

use App\User;
use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'delivery_id',
        'start_delivery_date',
        'complete_delivery_date',
        'confirm_delivery_date',
        'confirm_user_id'
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
    public function confirm_user()
    {
        return $this->belongsTo(User::class, 'confirm_user_id');
    }
}
