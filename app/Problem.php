<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Problem extends Model
{
    protected $fillable = ['id','name','diff_cmd','diff_arg','allow_practice','admin_note'];


    public function languages()
    {
        return $this->belongsToMany('App\Language')->withTimestamps()->withPivot('time_limit','memory_limit');
    }

    public function assignments()
    {
        return $this->belongsToMany('App\Assignment');
    }

    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
}
