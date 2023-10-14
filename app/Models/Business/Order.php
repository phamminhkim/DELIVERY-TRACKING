<?php

namespace App\Models\Business;

use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\Master\OrderStatus;
use App\Models\Master\Warehouse;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model
{
    use Uuids;

    protected $fillable = [
        'id', 'company_code', 'customer_id', 'sap_so_number', 'sap_so_created_date', 'sap_po_number', 'sap_do_number', 'status_id', 'warehouse_id', 'updated_at', 'is_draft'
    ];
    protected $casts = [
        'sap_so_created_date' => 'datetime',
        'updated_at' => 'datetime',
        'is_draft' => 'boolean'
    ];
    protected $hidden = [
        'company_code', 'customer_id', 'warehouse_id', 'status_id'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_draft', false);
    }

    // Apply the active scope by default
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function ($query) {
            $query->active();
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function detail()
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function receiver()
    {
        return $this->hasOne(OrderReceiver::class);
    }

    public function approved()
    {
        return $this->hasOne(OrderApproved::class);
    }

    public function sale()
    {
        return $this->hasOne(OrderSale::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function deliveries()
    {
        return $this->belongsToMany(Delivery::class, 'order_deliveries');
    }

    public function delivery_info()
    {
        return $this->hasOne(OrderDelivery::class, 'order_id', 'id');
    }

    public function driver_confirms()
    {
        return $this->hasMany(OrderDriverConfirm::class);
    }

    public function customer_reviews()
    {
        return $this->hasMany(OrderCustomerReview::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }

    public function filterCustomers($query, Request $request)
    {
        if ($request->filled('customer_ids')) {
            $query->whereIn('customer_id', $request->customer_ids);
        }
        return $query;
    }

    public function filterDeliveryPartners($query, Request $request)
    {
        if ($request->filled('delivery_partner_ids')) {
            $delivery_partner_ids = $request->delivery_partner_ids ?? [];
            $query->whereHas('deliveries', function ($query) use ($delivery_partner_ids) {
                $query->whereIn('delivery_partner_id', $delivery_partner_ids);
            });
        }
        return $query;
    }

    public function filterWarehouses($query, Request $request)
    {
        if ($request->filled('warehouse_ids')) {
            $query->whereIn('warehouse_id', $request->warehouse_ids);
        }
        return $query;
    }

    public function filterDistributionChannels($query, Request $request)
    {
        if ($request->filled('distribution_channel_ids')) {
            $distribution_channel_ids = $request->distribution_channel_ids;
            $query->whereHas('sale', function ($query) use ($distribution_channel_ids) {
                $query->whereIn('distribution_channel_id', $distribution_channel_ids);
            });
        }
        return $query;
    }
}
