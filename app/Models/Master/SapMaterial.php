<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SapMaterial extends Model
{
    protected $table = 'sap_materials';
    protected $fillable = [
        'sap_code',
        'sap_name'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
