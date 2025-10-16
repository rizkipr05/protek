<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Division extends Model
{
   
        use HasFactory, HasUlids, SoftDeletes;
    
        protected $fillable = [
            'name', 'description', 'is_active',
        ];
    
        protected $casts = [
            'is_active' => 'boolean',
        ];
    

    // relasi opsional
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function ditasks()
    {
        return $this->hasMany(\App\Models\Task::class);
    }
}
