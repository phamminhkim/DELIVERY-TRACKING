<?php

namespace App\Models\Business;

use App\Models\Master\SapMaterial;
use Illuminate\Database\Eloquent\Model;

class RawSoItem extends Model
{
    protected $fillable = [
        'raw_extract_item_id',
        'raw_so_header_id',
        'sap_material_id',
        'quantity',
        'note',
        'percentage',
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
