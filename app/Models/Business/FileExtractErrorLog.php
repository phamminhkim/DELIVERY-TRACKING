<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class FileExtractErrorLog extends Model
{
    protected $fillable = [
        'log',
        'file_extract_error_id',
    ];

    public function file()
    {
        return $this->belongsTo(FileExtractError::class);
    }
}
