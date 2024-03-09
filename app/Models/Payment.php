<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo{
        return $this->belongsTo('App\Models\Membership');
    }

    public function payment_type(): BelongsTo{
        return $this->belongsTo('App\Models\PaymentType');
    }

    public function getDateBuysAttribute($value)
    {
        return \Carbon\Carbon::create($value)->format("Y-m-d");
    }
     public function getDateBuysEndAttribute($value)
    {
        return \Carbon\Carbon::create($value)->format("Y-m-d");
    }

    protected $fillable = [
        'amount', 'date_buys', 'date_buys_end', 'user_id', 'membership_id', 'payment_type_id'
    ];
}
