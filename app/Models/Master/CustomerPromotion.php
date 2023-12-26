<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerPromotion extends Model
{

    protected $table = 'customer_promotions';
    public $timestamps = false;

    // Các trường (columns) trong bảng
    protected $fillable = [
        'customer_group_id',
        'customer_id',
        'sap_material_id',
        'is_active',
    ];
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sap_material()
    {
        return $this->belongsTo(SapMaterial::class);
    }
}
