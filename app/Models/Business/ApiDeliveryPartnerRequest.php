<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ApiDeliveryPartnerRequest extends Model
{
    protected $fillable = [
        'api_url',
        'api_method',
        'api_body_datas',
        'api_delivery_code_field',
    ];

    protected $casts = [
        'api_body_datas' => 'object',
    ];

    public $timestamps = false;

    public function config()
    {
        return $this->hasOne(ApiDeliveryPartnerConfig::class, 'api_request_id');
    }
}
