<?php

namespace App\Models\Business;

use App\Models\Master\ApiDeliveryPartnerMapping;
use Illuminate\Database\Eloquent\Model;

class ApiDeliveryPartnerConfig extends Model
{
    protected $fillable = [
        'delivery_partner_id',
        'warehouse_id',
        'api_mapping_id',
        'api_request_id',
        'api_response_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public $timestamps = false;

    public function delivery_partner()
    {
        return $this->belongsTo(DeliveryPartner::class);
    }

    public function mapping()
    {
        return $this->belongsTo(ApiDeliveryPartnerMapping::class, 'api_mapping_id');
    }

    public function request()
    {
        return $this->belongsTo(ApiDeliveryPartnerRequest::class, 'api_request_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
