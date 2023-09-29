<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class OrderApproved extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sap_so_finance_approval_date',
        'sap_do_posting_date'
    ];
    protected $casts = [
        'sap_so_finance_approval_date' => 'datetime',
        'sap_do_posting_date' => 'datetime'
    ];
    protected $hidden = [
        'order_id'
    ];
}
