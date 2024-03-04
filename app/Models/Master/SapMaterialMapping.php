<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SapMaterialMapping extends Model
{
    //no timestamps
    public $timestamps = false;

    //fillable
    protected $fillable = [
        'customer_material_id',
        'sap_material_id',
        'percentage',
        'customer_number',
        'conversion_rate_sap',
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
