<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProcessSaleItem extends Model
{
    protected $fillable = [
        'order_process_sale_id',
        'customer_key',
        'type',
        'customer_name',
        'barcode',
        'product_name',
        'price',
        'quantity',
        'specifications',
        'description',
        'count_order',
        'barcode_cty',
        'sap_code',
        'sap_name',
        'unit',
    ];
    public function orderProcessSale()
    {
        return $this->belongsTo(OrderProcessSale::class, 'order_process_sale_id');
    }
}
