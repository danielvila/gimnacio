<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concurrence extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function getEntryTimeAttribute($value)
    {
        return \Carbon\Carbon::create($value)->format("d-m-Y H:i");
    }

    public function getDepartureTimeAttribute($value)
    {
        if(is_null($value)){
            return '';
        }else{
            return \Carbon\Carbon::create($value)->format("d-m-Y H:i");
        }
    }

    protected $fillable = [
        'entry_time', 'departure_time', 'user_id'
    ];
}
