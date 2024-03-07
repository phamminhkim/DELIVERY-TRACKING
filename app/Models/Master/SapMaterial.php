<?php

namespace App\Models\Master;

use App\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Model;

class SapMaterial extends Model
{
    use FullTextSearch;
    protected $table = 'sap_materials';
    public $timestamps = false;
    protected $fillable = [
        // 'company_id',
        'unit_id',
        'bar_code',
        'sap_code',
        'name'

    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'sap_materials.sap_code',
        'sap_materials.name',
    ];

    // public function company()
    // {
    //     return $this->belongsTo(Company::class, 'company_id');
    // }
    public function unit()
    {
        return $this->belongsTo(SapUnit::class, 'unit_id');
    }

    public function mappings()
    {
        return $this->hasMany(SapMaterialMapping::class, 'sap_material_id');
    }

    public function customer_materials()
    {
        return $this->hasManyThrough(CustomerMaterial::class, SapMaterialMapping::class, 'sap_material_id', 'id', 'id', 'customer_material_id');
    }
}
