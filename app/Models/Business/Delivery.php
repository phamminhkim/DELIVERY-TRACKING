<?php

namespace App\Models\Business;

use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\Master\DeliveryPartner;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Delivery extends Model
{
    use Uuids;

    protected $fillable = [
        'delivery_code',
        'company_code',
        'delivery_partner_id',
        'customer_id',
        'address',
        'start_delivery_date',
        'estimate_delivery_date',
        'complete_delivery_date',
        'created_by'
    ];
    protected $hidden = [
        'company_code',
        'customer_id',
        'status_id',
        'warehouse_id',
        'delivery_partner_id',
        'created_by'
    ];
    protected $casts = [
        'start_delivery_date' => 'datetime:Y-m-d H:i:s',
        'complete_delivery_date' => 'datetime:Y-m-d H:i:s',
        'estimate_delivery_date' => 'datetime:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }
    public function partner()
    {
        return $this->belongsTo(DeliveryPartner::class, 'delivery_partner_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function pickup()
    {
        return $this->hasOne(DeliveryPickup::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_deliveries');
    }
    public function tokens()
    {
        return $this->hasMany(DeliveryToken::class);
    }
    public function primary_token()
    {
        return $this->hasOne(DeliveryToken::class)->where('is_primary', true);
    }
    public function timelines()
    {
        return $this->hasMany(DeliveryTimeline::class);
    }
}
