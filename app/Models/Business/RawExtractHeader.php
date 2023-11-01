<?php

namespace App\Models\Business;

use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawExtractHeader extends Model
{
    use SoftDeletes;
    //fillable
    protected $fillable = [
        'customer_id',
        'uploaded_file_id',
        'po_number',
        'po_date',
        'po_person',
        'po_phone',
        'po_delivery_date',
        'po_delivery_address',
        'po_note'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function uploaded_file()
    {
        return $this->belongsTo(UploadedFile::class);
    }

    public function raw_extract_items()
    {
        return $this->hasMany(RawExtractItem::class);
    }
}
