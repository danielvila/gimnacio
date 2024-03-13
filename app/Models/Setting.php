<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    public function getLogoAttribute()
    {
        if($this->attributes['logo'])
            return Storage::url($this->attributes['logo']);
        else
            return '';
    }

    public function getLogodeleteAttribute()
    {
        return $this->attributes['logo'];
    }

    protected $fillable = ['name', 'ruc', 'logo', 'description', 'dv', 'email', 'phone'];
}
