<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ExternalDeliveryCode extends Model
{

    protected $fillable = [
        'delivery_id',
        'external_delivery_code',
    ];
    public $timestamps = false;

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }
}
