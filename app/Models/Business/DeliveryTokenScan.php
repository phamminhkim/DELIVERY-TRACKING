<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class DeliveryTokenScan extends Model
{
    protected $fillable = [
        'token_id',
        'token',
        'scan_by',
        'scan_at',
        'is_success',
        'result'
    ];
    protected $hidden = [
        'id',
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
    public function token()
    {
        return $this->belongsTo(DeliveryToken::class);
    }
}
