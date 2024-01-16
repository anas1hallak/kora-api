<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [

        'group',
    
    ];



    protected $hidden = ['created_at','updated_at'];



    public function teams(){

        return $this->hasMany(Gteam::class);
    }
    public function matches(){

        return $this->hasMany(Gmatch::class);
    }

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }
}