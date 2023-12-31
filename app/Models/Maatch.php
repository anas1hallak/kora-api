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
    ];

    public function round(){

        return $this->belongsTo(Round::class);

    }
    




}
