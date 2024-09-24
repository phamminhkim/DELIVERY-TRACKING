<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
// use App\User;

class PoUploadFile extends Model
{
    protected $table = 'po_upload_files';
    protected $fillable = [
        'file_name',
        'path',
        'convert_po_uid',
        'created_by',
        'is_deleted'
    ];
}
