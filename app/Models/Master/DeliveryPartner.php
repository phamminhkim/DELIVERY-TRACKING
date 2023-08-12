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
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function distribution_channels()
    {
        return $this->belongsToMany(DistributionChannel::class, 'partner_channel');
    }

    public function users()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }
}
