<?php

namespace App\Models\Business;

use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Model;

class RawSoHeader extends Model
{
    protected $fillable = [
        'customer_id',
        'raw_extract_header_id',
        'uploaded_file_id',
        'po_number',
        'po_date',
        'po_person',
        'po_phone',
        'po_delivery_date',
        'po_delivery_address',
        'po_note',
        'note',
        'sap_so_number',
        'serial_number'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function raw_extract_header()
    {
        return $this->belongsTo(RawExtractHeader::class);
    }

    public function file()
    {
        return $this->belongsTo(UploadedFile::class);
    }
}
