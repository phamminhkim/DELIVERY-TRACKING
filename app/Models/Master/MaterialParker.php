<?php

namespace App\Models\Master;
use App\Traits\FullTextSearch;

use Illuminate\Database\Eloquent\Model;

class MaterialParker extends Model
{
    use FullTextSearch;
    protected $table = 'material_parkers';
    protected $fillable = [
        // 'customer_group_id',        
        'bar_code',
        'sap_code',
        'name',
        'is_active'
    ];


    protected $searchable = [
        'material_parkers.sap_code',
        'material_parkers.name',
        'material_parkers.bar_code',
    ];
}
