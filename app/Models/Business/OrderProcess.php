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
        return $this->hasMany(SoDataItem::class);
    }
    public function so_headers()
    {
        return $this->hasMany(SoHeader::class);
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
