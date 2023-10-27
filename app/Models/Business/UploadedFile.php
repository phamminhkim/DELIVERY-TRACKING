<?php

namespace App\Models\Business;

use App\Models\Master\UserMorph;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedFile extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'path',
        'is_handled',
        'batch_id',
        'status_id',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user_morphs()
    {
        return $this->morphMany(UserMorph::class, 'morphable');
    }

    public function raw_extract_items()
    {
        return $this->hasMany(RawExtractItem::class);
    }

    public function raw_extract_header(){
        return $this->hasOne(RawExtractHeader::class);
    }

    public function raw_so_headers(){
        return $this->hasMany(RawSoHeader::class);
    }

    public function status(){
        return $this->belongsTo(FileStatus::class);
    }
}
