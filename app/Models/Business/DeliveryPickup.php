<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class DeliveryPickup extends Model
{
    protected $fillable = [
        'pickup_at',
        'driver_phone',
        'driver_name',
        'driver_note',
        'driver_plate_number',
    ];
    protected $hidden = [
        'delivery_id',
    ];
    protected $casts = [
        'pickup_at' => 'datetime',
    ];
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
