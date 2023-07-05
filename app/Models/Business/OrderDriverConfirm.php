<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderDriverConfirm extends Model
{
    protected $fillable = [
        'confirm_date',
        'confirm_status',
        'driver_phone',
        'driver_name',
        'driver_note',
        'driver_plate_number',
    ];
    protected $hidden = [
        'order_id',
    ];
    protected $casts = [
        'confirm_date' => 'datetime',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function images()
    {
        return $this->morphMany(CmsImage::class, 'imageable', 'imageable_type', 'imageable_id', 'id');
    }
}
