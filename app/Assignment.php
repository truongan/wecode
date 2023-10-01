<?php

namespace App;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    protected $fillable = ['name', 'total_submits', 'open', 'score_board', 'javaexceptions', 'start_time', 'finish_time', 'extra_time', 'late_rule', 'participants', 'description', 'user_id' , 'language_ids'];
    protected $dates = ['start_time', 'finish_time'];

    public function problems()
    {
        return $this->belongsToMany('App\Problem')->withPivot('score','ordering','problem_name')->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo('App\User');
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

    public function cannot_edit(User $actor){
        // dd($actor->role->name);
        if ($actor->role->name == 'admin'){
            return false;
        } else if ($actor->role->name == 'head_instructor'){
            if ($this->user->id != $actor->id 
                && !$actor->lops()->with('assignments')->get()->pluck('assignments')->collapse()->pluck('id')->contains($this->id)
            ){
                return('You can only edit assignment you created or assignment belongs to one of your classes');
            }
            else {
                return false;
            }
        }
        else return('You do not have permission to edit assignment');
    }

    public function can_submit(User $user)
    {   
        $result = new class{};
        $result->error_message = 'Unknown error';
        $result->can_submit = FALSE;

        //2021-09-08 : An's note: leave it here till i found somewhere better
        // dd('shit');
        if ($user->trial_time
            && in_array( $user->role->name, ['student']) 
            && $user->created_at->addHours($user->trial_time) <=  Carbon::now()
        ){
            $user->role_id = 5; //Hopefully 5 mean guest.
            $user->save();
        }
        

        if (in_array( $user->role->name, ['guest']) ){
            $result->error_message = ' Guest can not make submissions. Contact site admin to upgrade your account ';
        }
        elseif (in_array( $user->role->name, ['student']) && $this->open == 0){
            // if assignment is closed, non-student users (admin, instructors) still can submit
            $result->error_message = 'Selected assignment is closed.';
        }
        elseif (!$this->started() && in_array( $user->role->name, ['student']) ){
            // non-student users can submit to not started assignments
            $result->error_message = 'Selected assignment has not started.';
        }
        elseif ($this->start_time < $this->finish_time
                && Carbon::now() > $this->finish_time->addSeconds( $this->extra_time))
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
        if (in_array( $user->role->name, ['admin']) ) return True;
        return in_array($user->id,$this->lops->pluck('users')->collapse()->pluck('id')->unique()->toArray());
    }

    public function started(){
        return strtotime(date("Y-m-d H:i:s")) >= strtotime($this->start_time) ; //now should be larger than start time
    }

    public function update_submissions_coefficient(){
        foreach ($this->submissions as $sub){
            ob_start();
            try 
            {
                $delay = $this->finish_time->diffInSeconds($sub->created_at,false);
                $extra_time = $this->extra_time;
                eval($this->late_rule);
            }
            catch (\Throwable $e) 
            {
                // dd($e);
                $coefficient = "error";
            }
            if (!isset($coefficient)  || !is_numeric($coefficient))
                $coefficient = "error";
            ob_end_clean();

            $sub->coefficient = $coefficient;
            $sub->save();
        }
    }

    public function is_finished(){
        $delay = $this->finish_time->diffInSeconds(Carbon::now(), false);
        return ($this->start_time < $this->finish_time &&  $delay > $this->extra_time);
    }
    public function eval_coefficient(){
        ob_start();
		try 
		{
            $delay = $this->finish_time->diffInSeconds(Carbon::now(), false);
            $extra_time = $this->extra_time;
			eval($this->late_rule);
		}
		catch (\Throwable $e) 
		{
            // dd($e);
			$coefficient = "error";
		}
		if (!isset($coefficient)  || !is_numeric($coefficient))
			$coefficient = "error";
		ob_end_clean();
		return $coefficient;
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

    // Reset final submissions choices
    public function reset_final_submission_choices(){
        $problem_score = $this->problems->pluck('pivot.score','id');
        $subs = $this->submissions()->oldest()->get()->keyBy('id');


        $final_subs = [];
        foreach ($subs as $sub){
            $key = $sub->user_id . "," . $sub->problem_id;
            $sub->is_final = 0;
            $change = true;
            if (isset($final_subs[$key])){
                $final = $subs[ $final_subs[$key] ];

                $final_score = ceil($final->pre_score * ($problem_score[$final->problem_id] ?? 0)/10000);
                $final_score = ceil($final_score * ($final->coefficient === 'error' ? 0 : $final->coefficient/100) );
                
                $sub_score = ceil($sub->pre_score * ($problem_score[$sub->problem_id] ?? 0)/10000);
                $sub_score = ceil($sub_score * ($sub->coefficient === 'error' ? 0 : $sub->coefficient/100) );
                
                if ($sub->pre_score == 10000){
                    if ($final->pre_score == 10000 && $sub_score <= $final_score) $change = false;
                } else {
                    if ($final->pre_score == 10000) $change = false;
                    else if ($sub_score <= $final_score) $change = false;
                }
                if ($change){
                    $final->is_final = 0;
                    $final->save();
                }
            }
            if ($change){
                $final_subs[$key] = $sub->id;
                $sub->is_final = 1;
            }
            $sub->save();
        }
    }

}
