<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Queue_item extends Model
{
    //
    protected $fillable = ['submission_id', 'type', 'processid'];

    public function submission()
    {
        return $this->belongsTo('App\Submission');
    }

    public function in_queue ($user_id, $assignment_id, $problem_id)
	{
		
		$query = $this->db->get_where('queue', array('username'=>$username, 'assignment'=>$assignment, 'problem'=>$problem));
		return ($query->num_rows() > 0);
	}
}
