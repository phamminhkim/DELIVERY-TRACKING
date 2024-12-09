<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProcessSaleBy extends Model
{
    protected $fillable = [
        'order_process_sale_id',
        'processing_by',
        'processing_at',
        'completed_at'
    ];
    public function orderProcessSale()
    {
        return $this->belongsTo(OrderProcessSale::class, 'order_process_sale_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'processing_by');
    }
}
