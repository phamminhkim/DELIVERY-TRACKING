<?php

namespace App;

use App\Models\Shared\Customer;
use App\Models\Shared\Employee;
use App\Models\Shared\Transporter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','provider_name', 'provider_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    public function transporter()
    {
        return $this->hasOne(Transporter::class);
    }
    
}
