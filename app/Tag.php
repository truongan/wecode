<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //
    protected $fillable = ['text'];
    public function problems()
    {
        return $this->belongsToMany('App\Problem');
    }
}
