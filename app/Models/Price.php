<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [

        'code',
        'price',
        
    ];
    
    
    protected $casts = [
        
    'price' => 'double',
  

    ];

    protected $hidden = ['created_at','updated_at'];

}
