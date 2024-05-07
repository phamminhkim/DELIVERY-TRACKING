<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoHeader extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'order_process_id',
        'customer_name',
        'customer_code',
        'note',
        'level2',
        'level3',
        'level4',
    ];

    public function order_process()
    {
        return $this->belongsTo(OrderProcess::class);
    }
    public function so_data_items()
    {
        return $this->hasMany(SoDataItem::class);
    }
}
