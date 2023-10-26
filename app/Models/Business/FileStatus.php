<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class FileStatus extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'code',
        'name',
    ];

}
