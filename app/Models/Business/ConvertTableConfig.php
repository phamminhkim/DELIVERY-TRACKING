<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ConvertTableConfig extends Model
{
    //no timestmaps
    public $timestamps = false;

    //fillable fields
    protected $fillable = [
        'method',
        'manual_patterns',
        'regex_pattern',
    ];

    //casts
    protected $casts = [
        'manual_patterns' => 'array',
    ];

    public function customer_group()
    {
        return $this->hasOneThrough(CustomerGroup::class, ExtractOrderConfig::class, 'convert_table_config_id', 'id', 'id', 'customer_group_id');
    }
}
