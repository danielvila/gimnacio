<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use HasFactory, SoftDeletes;

    public function payments(){
        return $this->hasMany('App\Models\Payment');
    }

    protected $fillable = [
        'name',
    ];
}
