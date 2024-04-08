<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MaterialCombo extends Model
{
    protected $table = 'material_combos';
    protected $fillable = ['sap_code', 'name', 'bar_code', 'is_deleted'];
}
