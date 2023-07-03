<?php

namespace App\Models\Business;

use App\Models\Master\Company;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Uuids;

    protected $fillable = [
        'company_code', 'customer_id', 'sap_so_number', 'sap_so_created_date', 'sap_po_number', 'sap_do_number', 'status_id', 'warehouse_id', 'updated_at'
    ];
    protected $casts = [
        'sap_so_created_date' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $hidden = [
        'company_code', 'customer_id', 'warehouse_id', 'status_id'
    ];

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

    public function driver_confirms()
    {
        return $this->hasMany(DriverConfirm::class);
    }

    public function customer_reviews()
    {
        return $this->hasMany(CustomerReview::class);
    }


    public function scopeStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }
}
