<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Head2HeadRequest extends Model
{
    use HasFactory;


    protected $fillable = [

       
        'Head2HeadMatch_id',
        'team1_id',
        'team2_id',
        'ibanNumber1',
        'ibanNumber2'
    ];
    
    
    protected $casts = [
        
        'team1_id' => 'integer',
        'team2_id' => 'integer',
        'Head2HeadMatch_id' => 'integer',


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

    public function H2HMatch()
    {
        
       return $this->belongsTo(Head2HeadMatch::class, 'Head2HeadMatch_id');

    }
}
