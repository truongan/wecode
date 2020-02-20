<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    
    protected $fillable = ['name', 'total_submits', 'open', 'score_board', 'javaexceptions', 'start_time', 'finish_time', 'extra_time', 'late_rule', 'participants', 'description' ];
    public function problems()
    {
        return $this->belongsToMany('App\Problem')->withPivot('score','ordering','problem_name')->withTimestamps();
    }
    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }
}
