<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ExtractDataConfig extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'method',
        'camelot_flavor',
        'is_merge_pages',
        'exclude_head_tables_count',
        'exclude_tail_tables_count',
        'specify_table_number',
        'is_specify_table_area',
        'table_area_info',
    ];

    protected $casts = [
        'is_merge_pages' => 'boolean',
        'exclude_head_tables_count' => 'integer',
        'exclude_tail_tables_count' => 'integer',
        'specify_table_number' => 'integer',
        'is_specify_table_area' => 'boolean',
        'table_area_info' => 'array',
    ];

    public function customer_group()
    {
        return $this->hasOneThrough(CustomerGroup::class, ExtractOrderConfig::class, 'extract_data_config_id', 'id', 'id', 'customer_group_id');
    }
}
