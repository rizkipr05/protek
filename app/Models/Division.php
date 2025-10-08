<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    // relasi opsional
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class);
    }
}
