<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequests extends Model
{
    use HasFactory;

    protected $fillable = [

        'team_id',
        'message',
        'user_id'
        
    ];

    protected $hidden = ['created_at','updated_at'];
    
    protected $casts = [
        
    'team_id' => 'integer',
    'user_id' => 'integer',

    ];


    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    
}
