<?php

namespace App\Models\Business;

use App\Models\Master\CustomerGroup;
use Illuminate\Database\Eloquent\Model;

class ExtractOrderConfig extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'customer_group_id',
        'extract_data_config_id',
        'convert_table_config_id',
        'restructure_data_config_id',
        'extract_header_config_id',
        'convert_table_header_config_id',
        'restructure_header_config_id',
        'name',
        'reference_id',
        'is_official',
        'is_convert_header',
        'convert_file_type',
        'is_config_group',
        'is_master_config_group',
        'is_slave_config_group',
        'master_config_group_id',
        'active'
    ];

    protected $casts = [
        'is_official' => 'boolean',
        'is_convert_header' => 'boolean',
        'is_config_group' => 'boolean',
        'is_master_config_group' => 'boolean',
        'is_slave_config_group' => 'boolean',
        'active' => 'boolean',
    ];

    public function customer_group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function extract_data_config()
    {
        return $this->belongsTo(ExtractDataConfig::class);
    }

    public function convert_table_config()
    {
        return $this->belongsTo(ConvertTableConfig::class);
    }

    public function restructure_data_config()
    {
        return $this->belongsTo(RestructureDataConfig::class);
    }

    public function extract_header_config()
    {
        return $this->belongsTo(ExtractDataConfig::class);
    }

    public function convert_table_header_config()
    {
        return $this->belongsTo(ConvertTableConfig::class);
    }

    public function restructure_header_config()
    {
        return $this->belongsTo(RestructureDataConfig::class);
    }

    public function reference_extract_order_config()
    {
        return $this->belongsTo(ExtractOrderConfig::class, 'reference_id');
    }

    public function slave_extract_order_configs()
    {
        return $this->hasMany(ExtractOrderConfig::class, 'master_config_group_id', 'id')
            ->where('is_official', true)->where('active', true)
            ->where('is_config_group', true)
            ->where('is_slave_config_group', true);
    }
}
