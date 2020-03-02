<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Queue_item extends Model
{
    //
    protected $fillable = ['submission_id', 'type', 'processid'];

    public function submission()
    {
        return $this->hasOne('App\Submission');
    }

    public function in_queue ($user_id, $assignment_id, $problem_id)
	{
		$queries = Queue_item::all();
        foreach ($queries as $query)
        {
            $query->submission->where(array('user_id' => $user_id, 'assignment_id' => $assignment_id, 'problem_id' => $problem_id))->get();
            if ($query->num_rows() > 0) return TRUE;
        }
		return FALSE;
	}

    public function add_to_queue($submit_info)
    {
        
    }
}
