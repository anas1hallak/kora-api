<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionshipRecord extends Model
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
        'firstWinner',
        'secondWinner'
    ];

protected $hidden = ['created_at','updated_at'];

protected $casts = [
    
'numOfParticipants' => 'integer',
'prize1' => 'double',
'prize2' => 'double',
'entryPrice' => 'double',


];

}
