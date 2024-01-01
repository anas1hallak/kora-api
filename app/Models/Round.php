<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $fillable = [

        'round',
    
    ];


    protected $hidden = ['created_at','updated_at'];


    public function matches(){

        return $this->hasMany(Maatch::class);
    }
    
}
