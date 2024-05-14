<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FullTextSearch;

class SapCompliance extends Model
{
    use FullTextSearch;
    protected $table = 'sap_compliances';
    // public $timestamps = false;
    protected $fillable = [
        'bar_code',
        'sap_code',
        'name',
        'unit_id',
        'quy_cach',
        'check_qc',
        'is_deleted',

    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = [
        'sap_compliances.sap_code',
        'sap_compliances.bar_code',
        'sap_compliances.name',
    ];
    public function unit()
    {
        return $this->belongsTo(SapUnit::class, 'unit_id');
    }
}
