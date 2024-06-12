<?php

namespace App\Models\Business;

use App\Models\Master\CustomerGroup;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrderProcess extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'customer_group_id',
        'serial_number',
        'title',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function so_data_items()
    {
        return $this->hasMany(SoDataItem::class)->orderBy('order');
    }
    public function so_headers()
    {
        return $this->hasMany(SoHeader::class);
    }
    public function not_sync_so_data_items()
    {
        // Lấy data chưa đồng bộ SAP
        return $this->hasMany(SoDataItem::class)->whereHas('so_header', function ($query) {
            $query->whereNull('so_uid')->where('is_syncing_sap', 0);
        });
    }
    public function is_sync_so_data($sap_so_number, $promotive_name)
    {
        // Kiểm tra data đã hoặc đang đồng bộ SAP
        $sync_data =  $this->hasMany(SoDataItem::class)->where('promotive_name', $promotive_name)
        ->whereHas('so_header', function ($query) use ($sap_so_number) {
            $query->where('sap_so_number', $sap_so_number)->where(function ($qu) {
                $qu->whereNotNull('so_uid')->orWhere('is_syncing_sap', 1);
            });
        });
        return $sync_data->count() > 0 ? true : false;
    }
    public function not_sync_so_headers()
    {
        // Lấy data chưa đồng bộ SAP
        return $this->hasMany(SoHeader::class)->whereNull('so_uid')->where('is_syncing_sap', 0);
    }
    public function sync_so_headers()
    {
        // Lấy data đã hoặc đang đồng bộ SAP
        return $this->hasMany(SoHeader::class)->whereNotNull('so_uid')->orWhere('is_syncing_sap', 1);
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name', 'username');
    }
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by')->select('id', 'name', 'username');
    }
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }


}
