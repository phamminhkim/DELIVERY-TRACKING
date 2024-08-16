<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThemeColor extends Model
{
    protected $fillable = [
        'so_data_item_id',
        'background',
        'text',
    ];

    protected $casts = [
        'background' => 'object',
        'text' => 'object',
    ];
}
