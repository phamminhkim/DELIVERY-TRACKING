<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerPromotion extends Model
{

    protected $table = 'customer_promotions';
    public $timestamps = false;

    // Các trường (columns) trong bảng
    protected $fillable = [
        'customer_material_id',
        'sap_material_id',
        'is_active',
    ];
    public function customer_material()
    {
        return $this->belongsTo(CustomerMaterial::class);
    }

    public function sap_material()
    {
        return $this->belongsTo(SapMaterial::class);
    }
}
