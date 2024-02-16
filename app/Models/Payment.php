<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function membership(){
        return $this->belongsTo('App\Models\Membership');
    }

    public function getDateBuysAttribute($value)
    {
        return \Carbon\Carbon::create($value)->format("Y-m-d");
    }

    protected $fillable = [
        'amount', 'date_buys', 'user_id', 'membership_id',
    ];
}
