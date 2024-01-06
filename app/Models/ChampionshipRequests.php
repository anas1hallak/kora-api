<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionshipRequests extends Model
{
    use HasFactory;

    protected $fillable = [

        'team_id',
        'message',
        'championship_id'
        
    ];


    protected $hidden = ['created_at','updated_at'];
}
