<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class RawExtractItem extends Model
{
    protected $fillable = [
        'customer_material_id',
        'quantity',
        'file_id'
    ];

    public function file()
    {
        return $this->belongsTo(UploadedFile::class);
    }

    public function customer_material()
    {
        return $this->belongsTo(CustomerMaterial::class);
    }
}
