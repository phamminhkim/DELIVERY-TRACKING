<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MaterialCombo extends Model
{
    protected $table = 'material_combos';
    protected $fillable = [
        'customer_group_id',
        'sap_code',
        'name',
        'bar_code',
        'is_deleted'];
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
