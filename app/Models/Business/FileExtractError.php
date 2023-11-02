<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class FileExtractError extends Model
{
    protected $fillable = [
        'uploaded_file_id',
        'extract_error_id',
        'is_reprogress',
    ];

    public function file()
    {
        return $this->belongsTo(UploadedFile::class);
    }

    public function extract_error()
    {
        return $this->belongsTo(ExtractError::class);
    }
    public function log()
    {
        return $this->hasOne(FileExtractErrorLog::class);
    }
}
