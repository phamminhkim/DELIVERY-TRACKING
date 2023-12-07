<?php

namespace App\Models\Business;

use App\Models\Master\CustomerMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawExtractItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'raw_extract_header_id',
        'customer_material_id',
        'quantity',
        'price',
        'amount',
        'file_id'
    ];

    public function file()
    {
        return $this->belongsTo(UploadedFile::class);
    }

    public function customer_material()
    {
        return $this->belongsTo(CustomerMaterial::class);
    }

    public function raw_extract_header()
    {
        return $this->belongsTo(RawExtractHeader::class);
    }

    public function raw_so_items()
    {
        return $this->hasMany(RawSoItem::class);
    }
}
