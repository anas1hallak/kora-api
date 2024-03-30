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
        'role_id',
        'isCoach',
        'team_id',
        'elo',
        
        
        ];

    protected $hidden = ['password','created_at','updated_at'];
    
    
     protected $casts = [
        
    'age' => 'integer',
    'playerNumber' => 'integer',
    'team_id' => 'integer',
    'role_id' => 'integer',
    'elo' => 'integer',


    ];
    
    

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
    
    public function formations()
    {
        return $this->hasMany(Formation::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Head2HeadMatchEvent::class);
    }
}

