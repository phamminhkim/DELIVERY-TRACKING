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
        // Config dành cho user
        return $this->hasMany(ExtractOrderConfig::class, 'customer_group_id', 'id')
            ->where('is_official', true)->where('active', true)
            ->where(function ($q) {
                // Không phải nhóm config
                $q->where('is_config_group', false)
                // Là nhóm master config
                ->orWhere(function ($q1) {
                    $q1->where('is_config_group', true)->where('is_master_config_group', true);
                });
            });
    }

    public function customer_materials()
    {
        return $this->hasMany(CustomerMaterial::class, 'customer_group_id', 'id');
    }

    public function admin_extract_order_configs()
    {
        // Config dành cho admin
        return $this->hasMany(ExtractOrderConfig::class, 'customer_group_id', 'id')
            ->where('is_official', true)->where('active', true)
            ->where(function ($q) {
                // Không phải nhóm config
                $q->where('is_config_group', false)
                // Là nhóm master config
                ->orWhere(function ($q1) {
                    $q1->where('is_config_group', true)->where('is_master_config_group', true);
                })
                // Là nhóm slave config
                ->orWhere(function ($q2) {
                    $q2->where('is_config_group', true)->where('is_slave_config_group', true);
                });
            });
    }
}
