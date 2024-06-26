<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [


        'teamName',
        'points',
        'wins',
        'loses',
        'rate',
        'termsAndConditions',
        'coachName',
        'coachPhoneNumber',
        'coachEmail',
        'user_id',
        
        ];
        
        
     protected $casts = [
        
    'points' => 'integer',
    'wins' => 'integer',
    'loses' => 'integer',
    'rate' => 'double',
    'user_id' => 'integer',

    ];    



    protected $hidden = ['created_at','updated_at'];

    public function players()
    {
        return $this->hasMany(User::class);
    }

    public function formation()
    {
        return $this->hasMany(Formation::class);
    }


    public function image(){

        return $this->hasOne(Teamimage::class);
    }

    public function requests()
    {
        return $this->hasMany(TeamRequests::class);
    }

    public function championship()
    {
        
       return $this->belongsToMany(Championship::class);

    }

    public function H2HMatch()
    {
        
        return $this->hasMany(Head2HeadMatch::class, 'team1_id')->orWhere('team2_id', $this->id);

    }

    public function events()
    {
        return $this->hasMany(Head2HeadMatchEvent::class);
    }


   
}
