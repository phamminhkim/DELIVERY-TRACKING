<?php

namespace App\Models\Business;

use App\Models\Master\Customer;
use App\Models\Master\Warehouse;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Batch extends Model
{
    use Uuids;
    protected $fillable = [
        'extract_order_config_id',
        'reference_batch_id',
        'customer_id',
        'company_code',
        'warehouse_id'
    ];

    public function files()
    {
        return $this->hasMany(UploadedFile::class);
    }

    public function extract_order_config()
    {
        return $this->belongsTo(ExtractOrderConfig::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
