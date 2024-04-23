<?php

namespace App\Models\Business;

use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProcess extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'serial_number',
        'title',
        'created_by',
        'updated_by',
    ];

    public function so_data_items()
    {
        return $this->hasMany(SoDataItem::class);
    }
    public function so_headers()
    {
        return $this->hasMany(SoHeader::class);
    }
}
