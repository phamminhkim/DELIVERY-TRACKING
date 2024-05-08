<?php

namespace App\Models\Master;
use App\Traits\FullTextSearch;

use Illuminate\Database\Eloquent\Model;

class MaterialCombo extends Model
{
    use FullTextSearch;
    protected $table = 'material_combos';
    protected $fillable = [
        'customer_group_id',
        'sap_code',
        'name',
        'bar_code',
        'is_active'
    ];


    protected $searchable = [
        'material_combos.sap_code',
        'material_combos.name',
        'material_combos.bar_code',
    ];
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
