<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SaleDistrict extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'code',
        'name'
    ];
}
