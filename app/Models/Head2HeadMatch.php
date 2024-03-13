<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Head2HeadMatch extends Model
{
    use HasFactory;


    protected $fillable = [

        'date',
        'time',
        'location',
        'stad',
        'team1_id',
        'team2_id',
        'winner',
        'status',
        'ibanNumber1',
        'ibanNumber2'

    ];
    
    
    protected $casts = [
        
        'team1_id' => 'integer',
        'team2_id' => 'integer',
        'winner' => 'integer',


    ];

    protected $hidden = ['created_at','updated_at'];


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

    public function H2HRequest()
    {
        
       return $this->hasOne(Head2HeadRequest::class);

    }

    public function events()
    {
    
       return $this->hasMany(Head2HeadMatchEvent::class);

    }

    
}
