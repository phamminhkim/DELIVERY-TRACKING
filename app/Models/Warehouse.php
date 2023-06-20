<?php

namespace App\Models;

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
