<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getBirthdayAttribute($value)
    {
        return \Carbon\Carbon::create($value)->format("Y-m-d");
    }


    protected $fillable = [
        'cedula', 'phone', 'address', 'birthday', 'user_id'
    ];
}
