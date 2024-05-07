<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MaterialCategoryType extends Model
{
    public $timestamps = false;
    protected $table = 'material_category_types';
    protected $fillable = ['name', 'is_deleted'];
    // public function material_combos()
    // {
    //     return $this->hasMany(MaterialCombo::class, 'customer_group_id', 'id');
    // }
    // public function material_donated()
    // {
    //     return $this->hasMany(MaterialDonated::class, 'sap_code', 'id');
    // }
}
