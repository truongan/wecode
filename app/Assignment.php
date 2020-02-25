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

    public function lops()
    {
        return $this->belongsToMany('App\Lop');
    }

    public function can_submit($assignment)
    {
        $result->error_message = 'Uknown error';
        $result->can_submit = FALSE;

        if (in_array( Auth::user()->role->name, ['student']) && $assignment->open == 0){
            // if assignment is closed, non-student users (admin, instructors) still can submit
            $result->error_message = 'Selected assignment is closed.';
        }
        elseif (!started($assignment)){
            // non-student users can submit to not started assignments
            $result->error_message = 'Selected assignment has not started.';
        }
        elseif (strtotime($assignment->start_time) < strtotime($assignment->finish_time)
                && strtotime(date("Y-m-d H:i:s")) > strtotime($assignment->finish_time) + $assignment->extra_time)
        {
            // deadline = finish_time + extra_time
            // but if start time is before finish time, the deadline is NEVER
            $result->error_message =  'Selected assignment has finished.';
        }
        elseif ( !is_participant($assignment, Auth::user()->username) )
            $result->error_message = 'You are not registered for submitting.';
        else{
            $result->error_message = 'none';
            $result->can_submit = TRUE;
        }
        return $result;
    }

    public function is_participant($assignment, $username)
    {
        $lops = $assignment->lops;

    }

    public function started($assignment){
        return strtotime(date("Y-m-d H:i:s")) >= strtotime($assignment->start_time) //now should be larger than start time
                || !in_array( Auth::user()->role->name, ['student']; ///instructor can view assignment before start time
    }
}
