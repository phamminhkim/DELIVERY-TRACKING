<?php

namespace App\Models\Master;

use App\Models\Business\ExtractDataConfig;
use App\Models\Business\ExtractOrderConfig;
use App\Models\Business\RestructureDataConfig;
use App\Models\Business\ConvertTableConfig;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_group_pivots', 'customer_group_id', 'customer_id');
    }

    public function extract_data_config()
    {
        return $this->hasOneThrough(ExtractDataConfig::class, ExtractOrderConfig::class, 'customer_group_id', 'id', 'id', 'extract_data_config_id');
    }

    public function convert_table_config()
    {
        return $this->hasOneThrough(ConvertTableConfig::class, ExtractOrderConfig::class, 'customer_group_id', 'id', 'id', 'convert_table_config_id');
    }

    public function restructure_data_config()
    {
        return $this->hasOneThrough(RestructureDataConfig::class, ExtractOrderConfig::class, 'customer_group_id', 'id', 'id', 'restructure_data_config_id');
    }
}
