<?php

namespace App\Models\Business;

use App\Models\Master\SapMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawSoItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'raw_extract_item_id',
        'raw_so_header_id',
        'sap_material_id',
        'quantity',
        'price',
        'amount',
        'note',
        'percentage',
        'is_promotive',
    ];

    protected $casts = [
        'is_promotive' => 'boolean',
    ];
    public function raw_extract_item()
    {
        return $this->belongsTo(RawExtractItem::class);
    }
    public function raw_so_header()
    {
        return $this->belongsTo(RawSoHeader::class);
    }

    public function sap_material()
    {
        return $this->belongsTo(SapMaterial::class);
    }
}
