<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function membership(){
        return $this->belongsTo('App\Models\Membership');
    }

    public function payment_type(){
        return $this->belongsTo('App\Models\PaymentType');
    }

    public function getDateBuysAttribute($value)
    {
        return \Carbon\Carbon::create($value)->format("Y-m-d");
    }

    protected $fillable = [
        'amount', 'date_buys', 'user_id', 'membership_id', 'payment_type_id'
    ];
}
