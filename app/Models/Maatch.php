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
        'round_id',
        'team1_id',
        'team2_id',
        'winner'
    ];

    protected $hidden = ['created_at','updated_at'];
    

    public function round(){

        return $this->belongsTo(Round::class);

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
