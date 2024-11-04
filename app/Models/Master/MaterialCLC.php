<?php

namespace App\Models\Master;
use App\Traits\FullTextSearch;

use Illuminate\Database\Eloquent\Model;

class MaterialCLC extends Model
{
    use FullTextSearch;
    protected $table = 'material_clcs';
    protected $fillable = [
        'customer_group_id',
        'sap_code',
        'name',
        'bar_code',
        'is_active'
    ];


    protected $searchable = [
        'material_clcs.sap_code',
        'material_clcs.name',
        'material_clcs.bar_code',
    ];
    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
