<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    public function problems()
    {
        return $this->belongsToMany('App\Problem');
    }
}
