<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProcessSale extends Model
{
    protected $fillable = [
        'title',
        'central_branch',
        'description',
        'status',
        'created_by',
        'code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function orderProcessSaleBy()
    {
        return $this->belongsTo(OrderProcessSaleBy::class, 'id' ,'order_process_sale_id');
    }
    public function orderProcessSaleItems()
    {
        return $this->hasMany(OrderProcessSaleItem::class, 'order_process_sale_id');
    }
    public function orderProcessSaleReceive()
    {
        return $this->hasOne(OrderProcessSaleReceive::class, 'order_process_sale_id');
    }
}
