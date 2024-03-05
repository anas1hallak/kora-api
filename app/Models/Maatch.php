<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maatch extends Model
{
    use HasFactory;

    protected $fillable = [

        'date',
        'time',
        'location',
        'stad',
        'position',
        'round_id',
        'team1_id',
        'team2_id',
        'winner'
    ];

    protected $hidden = ['created_at','updated_at'];
    
     protected $casts = [
        
    'position' => 'integer',
    'round_id' => 'integer',
    'team1_id' => 'integer',
    'team2_id' => 'integer',
    'winner' => 'integer',


    ];

    public function round(){

        return $this->belongsTo(Round::class);

    }
    
    public function nextRoundMatch()
    {
        return $this->hasOne(Maatch::class, 'previous_match_id');
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function teams()
    {
        return [
            'team1' => $this->team1,
            'team2' => $this->team2,
        ];
    }
    




}
