<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SapMaterial extends Model
{
    protected $table = 'sap_materials';
    protected $fillable = [
        'company_id',
        'unit_id',
        'sap_code',
        'name'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function unit()
    {
        return $this->belongsTo(SapUnit::class, 'unit_id');
    }
}
