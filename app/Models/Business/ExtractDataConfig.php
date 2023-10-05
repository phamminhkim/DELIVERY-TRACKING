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
    ];

    protected $casts = [
        'is_merge_pages' => 'boolean',
        'exclude_head_tables_count' => 'integer',
        'exclude_tail_tables_count' => 'integer',
    ];

    public function customer_group()
    {
        return $this->hasOneThrough(CustomerGroup::class, ExtractOrderConfig::class, 'extract_data_config_id', 'id', 'id', 'customer_group_id');
    }
}
