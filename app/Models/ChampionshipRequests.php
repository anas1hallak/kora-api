<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionshipRequests extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'team_id',
        'championship_id',
        'teamName',
        'coachName',
        'ibanNumber',
        'coachPhoneNumber',
        'teamImage',
        
    ];


    protected $hidden = ['created_at','updated_at'];
}
