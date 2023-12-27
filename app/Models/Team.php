<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [


        'teamName',
        'points',
        'wins',
        'termsAndConditions',
        'coachName',
        'user_id',
        
        ];



    protected $hidden = ['created_at','updated_at'];

    public function players()
    {
        return $this->hasMany(User::class);
    }
}
