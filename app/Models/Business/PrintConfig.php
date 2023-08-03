<?php

namespace App\Models\Business;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PrintConfig extends Model
{
    //
    protected $fillable = ['name', 'config', 'user_id'];
    protected $hidden = ['user_id'];

    public $timestamps = false;
    public function user(){
        return $this->belongsTo(User::class);
    }
}