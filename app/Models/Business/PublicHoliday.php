<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'start_holiday_date',
        'end_holiday_date'
    ];

    protected $cats = [
        'start_holiday_date' => 'datetime',
        'end_holiday_date' => 'datetime'
    ];
}
