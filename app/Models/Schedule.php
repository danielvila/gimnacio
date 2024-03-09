<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function clients(){
        return $this->belongsToMany('App\Models\User');
    }

    public function routine(){
        return $this->belongsTo('App\Models\Routine');
    }

    public function getDayOfWeekAttribute($value)
    {
        return Carbon::now()->setISODate(2024, 1, $value)->dayName;
    }

    public function getNumDayOfWeekAttribute()
    {
        return $this->attributes['day_of_week'];
    }

    public function getHourAttribute($value)
    {
        return Carbon::parse($value)->format('h:i A');
    }

    public function getNumHourAttribute($value)
    {
        return $this->attributes['hour'];
    }

    protected $fillable = ['day_of_week', 'hour', 'description', 'user_id', 'routine_id'];

    protected $appends = ['NumDayOfWeek', 'NumHour'];
}
