<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class RawPoDataItem extends Model
{
    protected $fillable = [
        'raw_po_header_id',
        'customer_sku_code',
        'customer_sku_name',
        'customer_sku_unit',
        'quantity1_po',
        'quantity2_po',
        'price_po',
        'amount_po',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function raw_po_header()
    {
        return $this->belongsTo(RawPoHeader::class);
    }
}
