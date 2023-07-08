<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class DeliveryTimeline extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'delivery_partner_id',
        'timestamp',
        'event',
        'description',
    ];
    protected $hidden = [
        'id',
        'delivery_id',
    ];
    protected $casts = [
        'timestamp' => 'datetime'
    ];
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
