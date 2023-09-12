<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class SapUnit extends Model
{
    public $timestamps = false;
    protected $table = 'sap_units';
    protected $fillable = [
        'unit_code'
    ];
}
