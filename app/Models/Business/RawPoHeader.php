<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class RawPoHeader extends Model
{
    protected $fillable = [
        'po_number',
        'convert_po_uid',
        'po_delivery_date',
        'sap_so_number',
        'customer_name',
        'customer_code',
        'note',
        'level2',
        'level3',
        'level4',
        'created_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function raw_po_data_items()
    {
        return $this->hasMany(RawPoDataItem::class);
    }
    public function po_upload_file()
    {
        return $this->belongsTo(PoUploadFile::class);
    }
}
