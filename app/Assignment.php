<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    protected $fillable = ['name', 'total_submits', 'open', 'score_board', 'javaexceptions', 'start_time', 'finish_time', 'extra_time', 'late_rule', 'participants', 'description' ];
    protected $dates = ['start_time', 'finish_time'];

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

    public function scoreboard()
    {
        return $this->hasOne('App\Scoreboard');
    }

    public function can_submit(User $user)
    {   
        $result = new class{};
        $result->error_message = 'Uknown error';
        $result->can_submit = FALSE;

        if (in_array( $user->role->name, ['student']) && $this->open == 0){
            // if assignment is closed, non-student users (admin, instructors) still can submit
            $result->error_message = 'Selected assignment is closed.';
        }
        elseif (!$this->started() && in_array( $user->role->name, ['student']) ){
            // non-student users can submit to not started assignments
            $result->error_message = 'Selected assignment has not started.';
        }
        elseif (strtotime($this->start_time) < strtotime($this->finish_time)
                && strtotime(date("Y-m-d H:i:s")) > strtotime($this->finish_time) + $this->extra_time)
        {
            // deadline = finish_time + extra_time
            // but if start time is before finish time, the deadline is NEVER
            $result->error_message =  'Selected assignment has finished.';
        }
        elseif ( !$this->is_participant($user) )
            $result->error_message = 'You are not registered for submitting.';
        else
        {
            $result->error_message = 'none';
            $result->can_submit = TRUE;
        }
        return $result;
    }

    public function is_participant($user)
    {   
        if ($this->id == 0) return True;
        if (in_array( $user->role->name, ['admin', 'head_instructor']) ) return True;
        return in_array($user->id,$this->lops->pluck('users')->collapse()->pluck('id')->unique()->toArray());
    }

    public function started(){
        return strtotime(date("Y-m-d H:i:s")) >= strtotime($this->start_time) ; //now should be larger than start time
    }

    public static function assignment_info($assignment_id)
    {
        $query = Assignment::where('id', $assignment_id);
        if ($query->count() != 1)
            return array(
                'id' => 0,
                'name' => "instructors'submit",
                'finish_time' => 0,
                'extra_time' => 0,
                'problems' => 0,
                'open' => 0,
                'total_submits' => $query->submissions->count(),
            );
        
        return $query->first();
    }
}
