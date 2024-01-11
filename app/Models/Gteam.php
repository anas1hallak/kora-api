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
    ];


    protected $hidden = ['created_at','updated_at'];

}
