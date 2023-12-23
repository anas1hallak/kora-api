<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['fullName', 'phoneNumber', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }
}

