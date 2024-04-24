<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoDataItem extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'so_number',
        'order_process_id',
        'so_header_id',
        'barcode',
        'sku_sap_code',
        'sku_sap_name',
        'sku_sap_unit',
        'is_promotive',
        'note',
        'customer_sku_code',
        'customer_sku_name',
        'customer_sku_unit',
        'quantity1_po',
        'quantity2_po',
        'is_inventory',
        'inventory_quantity',
        'price_po',
        'amount_po',
    ];

    protected $casts = [
        'is_promotive' => 'boolean',
        'is_inventory' => 'boolean',
    ];
    public function order_process()
    {
        return $this->belongsTo(OrderProcess::class);
    }
    public function so_header()
    {
        return $this->belongsTo(SoHeader::class);
    }
}
