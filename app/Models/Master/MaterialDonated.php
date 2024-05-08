<?php

namespace App\Models\Master;

use App\Traits\FullTextSearch;


use Illuminate\Database\Eloquent\Model;

class MaterialDonated extends Model
{
    use FullTextSearch;

    protected $table = 'material_donateds';
    protected $fillable = [
        'sap_code',
        'name',
        'is_active'
    ];
    protected $searchable = [
        'material_donateds.sap_code',
        'material_donateds.name',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
