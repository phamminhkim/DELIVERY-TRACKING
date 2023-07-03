<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Delivery extends Model
{
    use Uuids;

    protected $fillable = [
        'company_code',
        'delivery_partner_id',
        'sap_so_number',
        'sap_so_created_date',
        'sap_po_number',
        'sap_do_number',
        'created_by'
    ];
    protected $hidden = [
        'company_code',
        'customer_id',
        'status_id',
        'warehouse_id',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_deliveries');
    }
    public function tokens()
    {
        return $this->hasMany(DeliveryToken::class);
    }
}
