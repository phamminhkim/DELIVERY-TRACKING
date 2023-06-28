<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'sap_so_number',
        'sap_so_created_date',
        'sap_po_number',
        'sap_do_number',
    ];
    protected $hidden = [
        'company_id',
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
    public function delivery_tokens()
    {
        return $this->hasMany(DeliveryToken::class);
    }
}
