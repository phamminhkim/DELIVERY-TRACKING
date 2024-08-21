<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class UserFieldTable extends Model
{
    // protected $table = 'user_field_tables';
    protected $fillable = ['user_id', 'field_table', 'version'];
    protected $casts = [
        'field_table' => 'array'
    ];
}
