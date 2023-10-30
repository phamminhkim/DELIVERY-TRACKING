<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class ExtractError extends Model
{
    public $timestamps = false;
    protected $table = 'extract_errors';

    protected $fillable = [
        'code',
        'name',
    ];
}
