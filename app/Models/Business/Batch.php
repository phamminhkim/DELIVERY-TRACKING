<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Batch extends Model
{
    use Uuids;
    protected $fillable = [
        'extract_order_config_id',
        'customer_id', 
        'company_code'
    ];

    public function files()
    {
        return $this->hasMany(UploadedFile::class);
    }

    public function extract_order_config()
    {
        return $this->belongsTo(ExtractOrderConfig::class);
    }
}
