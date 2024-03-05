<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gteam extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'team_id',
        'teamName',
        'points',
        'goals'
    ];
    
    


    protected $hidden = ['created_at','updated_at'];
    
    
    protected $casts = [
    
    'group_id' => 'integer',
    'team_id' => 'integer',
    'points' => 'integer',
    'goals' => 'integer',
   

    ];


    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
