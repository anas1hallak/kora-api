<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Head2HeadMatchEvent extends Model
{
    use HasFactory;

    protected $fillable = [

       
        'Head2HeadMatch_id',
        'team_id',
        'user_id',
        'time',
        'type'
    ];
    
    
    protected $casts = [
        
        'user_id' => 'integer',
        'team_id' => 'integer',
        'Head2HeadMatch_id' => 'integer',


    ];

    protected $hidden = ['created_at','updated_at'];



    public function H2HMatch()
    {
        
       return $this->belongsTo(Head2HeadMatch::class);

    }


    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
