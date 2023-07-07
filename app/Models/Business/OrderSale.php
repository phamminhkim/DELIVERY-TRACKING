<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderSale extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'distribution_channel_id', 'sale_district_id', 'sale_group_id'
    ];
    protected $hidden = [
        'order_id',
        'distribution_channel_id', 'sale_district_id', 'sale_group_id'
    ];

    public function distribution_channel()
    {
        return $this->belongsTo(DistributionChannel::class);
    }

    public function sale_district()
    {
        return $this->belongsTo(SaleDistrict::class);
    }

    public function sale_group()
    {
        return $this->belongsTo(SaleGroup::class);
    }
}
