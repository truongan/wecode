<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lop extends Model
{
    //
    protected $fillable = ['name', 'open'];
    function users(){
        return $this->belongsToMany('App\User');
    }
    function assignments(){
        return $this->belongsToMany('App\Assignment');
    }
}
