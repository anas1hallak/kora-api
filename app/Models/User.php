<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [

        'fullName',
        'phoneNumber',
        'password',
        'email',
        'age',
        'nationality',
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


    public function team(){

        return $this->belongsTo(Team::class);
    }
    

    public function image(){

        return $this->hasOne(Image::class);
    }

    
}

