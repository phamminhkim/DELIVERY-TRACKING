<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class DeliveryToken extends Model
{
    protected $fillable = [
        'token',
        'is_primary',
    ];
    protected $hidden = [
        'delivery_id',
        'delivery_partner_id',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'is_primary' => 'boolean'
    ];
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
    public function delivery_partner()
    {
        return $this->belongsTo(DeliveryPartner::class);
    }
}
