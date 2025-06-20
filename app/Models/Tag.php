<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //
    protected $fillable = ['text'];
    public function problems()
    {
        return $this->belongsToMany('App\Models\Problem');
    }
}
