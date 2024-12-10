<?php

namespace App\Models\Master;
use App\Traits\FullTextSearch;

use Illuminate\Database\Eloquent\Model;

class MaterialBundle extends Model
{
    use FullTextSearch;
    protected $table = 'material_bundles';
    protected $fillable = [
        'customer_group_id',
        'sap_code',
        'name',
        'bar_code',
        'is_active'
    ];


    protected $searchable = [
        'material_bundles.sap_code',
        'material_bundles.name',
        'material_bundles.bar_code',
    ];
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
