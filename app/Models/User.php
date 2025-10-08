<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name','username','email','password','role', 'divisi_id', 'is_active',
    ];

    protected $hidden = ['password','remember_token'];
    public function division(){ return $this->belongsTo(\App\Models\Division::class); }
}
