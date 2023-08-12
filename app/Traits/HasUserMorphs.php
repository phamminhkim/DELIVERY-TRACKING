<?php

namespace App\Traits;

use App\Models\Master\UserMorph;

trait HasUserMorphs
{
    public function userMorphs()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }
}
