<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    public $timestamps = false;
    protected $fillable = [
        "code",
        "name",
        "company_code",
        "is_active"
    ];
    protected $casts = [
        "is_active" => "boolean"
    ];
}
