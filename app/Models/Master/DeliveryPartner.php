<?php

namespace App\Models\Master;

use App\User;
use Illuminate\Database\Eloquent\Model;

class DeliveryPartner extends Model
{
    protected $fillable = [
        'code',
        'name',
        'api_url',
        'api_key',
        'api_secret',
        'is_external',
        'is_active',
        'api_body_datas',
        'api_delivery_code_field'
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_active' => 'boolean',
        'api_body_datas' => 'object'
    ];

    public function distribution_channels()
    {
        return $this->belongsToMany(DistributionChannel::class, 'partner_channel');
    }

    public function mapping()
    {
        return $this->hasOne(ApiDeliveryPartnerMapping::class);
    }
    public function users()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }
}
