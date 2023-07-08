<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class DeliveryTokenScan extends Model
{
    protected $fillable = [
        'delivery_id',
        'token_id',
        'token',
        'scan_by',
        'scan_at',
        'is_success',
        'result'
    ];
    protected $hidden = [
        'id',
        'delivery_id',
        'token_id',
    ];
    protected $casts = [
        'is_success' => 'boolean',
        'scan_at' => 'datetime'
    ];
    public function scanner()
    {
        return $this->belongsTo(User::class, 'scan_by');
    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
