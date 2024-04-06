<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRequests extends Model
{
    use HasFactory;


    protected $fillable = [

        'team_id',
        'fullName',
        'nationality',
        'placeOfPlayer',
        'user_id',
        'isSeen'
        
    ];


    protected $hidden = ['created_at','updated_at'];
    
     protected $casts = [
        
    'team_id' => 'integer',
    'user_id' => 'integer',

    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
