<?php

namespace App\Models\Master;

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
}
