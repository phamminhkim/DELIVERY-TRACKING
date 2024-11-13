<?php

namespace App\Models\Business;

use App\Models\Master\CustomerPartner;
use App\Models\Master\UserMorph;
use App\Models\Master\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoHeader extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'order_process_id',
        'warehouse_id',
        'shipping_id',
        'so_uid',
        'sap_so_number',
        'so_sap_note',
        'po_number',
        'po_delivery_date',
        'customer_name',
        'customer_code',
        'note',
        'level2',
        'level3',
        'level4',
        'sync_sap_status',
        'is_syncing_sap',
        'convert_po_uid',
    ];

    public function order_process()
    {
        return $this->belongsTo(OrderProcess::class);
    }
    public function so_data_items()
    {
        return $this->hasMany(SoDataItem::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->select('id', 'code');
    }
    public function user_morphs()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }
    public function raw_po_header()
    {
        return $this->hasOne(RawPoHeader::class, 'po_number', 'po_number');
    }
    // public function customer_partners()
    // {
    //     return $this->belongsTo(CustomerPartner::class, 'customer_code')->select('id', 'code');
    // }
}
