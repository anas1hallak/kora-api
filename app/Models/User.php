<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use  Notifiable;

    protected $fillable = [
        'fullName',
        'phoneNumber',
        'password',
        'email',
        'playerNumber',
        'placeOfPlayer',
        'selected',
        'elo',
        
        
        ];

    protected $hidden = ['password','created_at','updated_at'];

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }



    public function image(){

        return $this->hasOne(Image::class);
    }

    
}

