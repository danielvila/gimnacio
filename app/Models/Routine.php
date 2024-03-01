<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Routine extends Model
{
    use HasFactory, SoftDeletes;

    public function schudeles(){
        return $this->hasMany('App\Models\Schedule');
    }

    protected $fillable = [
        'name', 'description', 
    ];
}
