<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    use HasFactory;


    protected $fillable = [
            'championshipName',
            'numOfParticipants',
            'prize1',
            'prize2',
            'entryPrice',
            'startDate',
            'endDate',
            'termsAndConditions',
            'status',
            'firstWinner',
            'secondWinner'
        ];

    protected $hidden = ['created_at','updated_at'];

    public function teams(){

        return $this->belongsToMany(Team::class);

    }

    public function rounds(){

        return $this->hasMany(Round::class);
    }

    public function groups(){

        return $this->hasMany(Group::class);
    }

    public function image(){

        return $this->hasOne(Championshipimage::class);
    }

    
    public function requests(){

        return $this->hasMany(ChampionshipRequests::class);
    }

}
