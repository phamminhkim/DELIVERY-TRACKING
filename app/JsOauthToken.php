<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JsOauthToken extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'expired_time',
        'refresh_token_expire_time',
        'company_id',
    ];
}
