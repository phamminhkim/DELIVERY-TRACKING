<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class RestructureDataConfig extends Model
{
    //no timestmaps
    public $timestamps = false;

    //fillable fields
    protected $fillable = [
        'method',
        'structure',
    ];

    //casts
    protected $casts = [
        'structure' => 'array',
    ];

    public function customer_group()
    {
        return $this->hasOneThrough(CustomerGroup::class, ExtractOrderConfig::class, 'restructure_data_config_id', 'id', 'id', 'customer_group_id');
    }
}
