<?php

namespace App;

use App\Business\PrintConfig;
use App\Models\Master\DeliveryPartner;
use App\Traits\HasUserMorphs;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable //implements MustVerifyEmail  //Tạm remove bắt buộc verify
{
    use HasUserMorphs;
    use Notifiable, HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'active', 'avatar','phone_number','username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function print_configs(){
        return $this->hasMany(PrintConfig::class);
    }

    public function delivery_partners()
    {
         return $this->morphedByMany(DeliveryPartner::class, 'morphable', 'user_morphs');
    }
}
