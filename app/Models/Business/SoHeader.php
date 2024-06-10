<?php

namespace App\Models\Business;

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
        'is_sync_sap',
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
        return $this->belongsTo(Warehouse::class);
    }
    public function user_morphs()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }
}
