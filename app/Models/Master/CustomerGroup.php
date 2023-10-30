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

    public function extract_order_configs()
    {
        return $this->hasMany(ExtractOrderConfig::class, 'customer_group_id', 'id');
    }

    public function customer_materials()
    {
        return $this->hasMany(CustomerMaterial::class, 'customer_group_id', 'id');
    }
}
