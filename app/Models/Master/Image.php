<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $hidden = [
        'imageable_id',
        'imageable_type',
        'created_at',
        'updated_at'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}
