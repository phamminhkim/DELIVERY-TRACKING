<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ApiDeliveryPartnerMapping extends Model
{
    protected $fillable = [
        'delivery_partner_id',
        'root_data_field',
        'is_root_string',
        'start_delivery_date',
        'complete_delivery_date',
    ];
    public $timestamps = false;

    //casts
    protected $casts = [
        'is_root_string' => 'boolean',

    ];

    public function config()
    {
        return $this->hasOne(ApiDeliveryPartnerConfig::class, 'api_mapping_id');
    }
}
