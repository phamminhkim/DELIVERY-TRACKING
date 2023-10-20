<?php

namespace App\Models\Master;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserMorph extends Model
{
    protected $fillable = [
        'user_id'
    ];
    public function morphable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
