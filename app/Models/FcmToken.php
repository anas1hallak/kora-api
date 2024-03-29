<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'fcmToken'
    
    ];

    protected $hidden = ['created_at','updated_at'];
    
    protected $casts = [
        
    'user_id' => 'integer',


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
