<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProcessSaleReceive extends Model
{
    protected $fillable = [
        'order_process_sale_id',
        'received_by',
        'received_at',
    ];
    public function orderProcessSale()
    {
        return $this->belongsTo(OrderProcessSale::class, 'order_process_sale_id');
    }
    public function received()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
