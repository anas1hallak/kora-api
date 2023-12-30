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
        'starteDate',
        'endDate',
    ];

    public function teams(){

        return $this->belongsToMany(Team::class);
    }

    public function image(){

        return $this->hasOne(Championshipimage::class);
    }

    
    public function requests(){

        return $this->hasMany(ChampionshipRequests::class);
    }

}
