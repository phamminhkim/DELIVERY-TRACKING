<?php

namespace App\Models\Business;

use App\Models\Master\UserMorph;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UploadedFile extends Model
{
    protected $fillable = [
        'path',
        'is_handled',
        'batch_id',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function users()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }

    public function raw_extract_items()
    {
        return $this->hasMany(RawExtractItems::class);
    }
}
