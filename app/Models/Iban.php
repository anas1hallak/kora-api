<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iban extends Model
{
    use HasFactory;

    protected $fillable = [
        'ibanNumber',
        'accountName'
    ];

    protected $hidden = ['created_at','updated_at'];
}
