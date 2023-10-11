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
        'name'
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
}
