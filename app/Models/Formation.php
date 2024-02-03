<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;



    protected $fillable = [

        'team_id',
        'user_id',
        'position',
        'fullName',
        'imagePath',
    
        
        
        ];

    protected $hidden = ['created_at','updated_at'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
