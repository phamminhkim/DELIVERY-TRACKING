<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        "code",
        "name",
        "is_active"
    ];
    protected $casts = [
        "is_active" => "boolean"
    ];
}
