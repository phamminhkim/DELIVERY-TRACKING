<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CustomerMaterial extends Model
{
    public $timestamps = false;

    //fillable
    protected $fillable = [
        'customer_group_id',
        'customer_sku_code',
        'customer_sku_name',
        'customer_sku_unit',
    ];

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function sap_materials()
    {
        return $this->hasManyThrough(SapMaterial::class, SapMaterialMapping::class, 'customer_material_id', 'id', 'id', 'sap_material_id');
    }

    public function mappings()
    {
        return $this->hasMany(SapMaterialMapping::class, 'customer_material_id');
    }
}
