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

    /**
	 * If the number of queue item being processed is less than limit
	 * Returns the first item of the queue that are not being processed
	 */
	public static function acquire($limit)
	{
		DB::beginTransaction(); // We use the queue table as a mutex, so this function must be atomic
		$item = NULL;
		if(Queue_item::whereNotNull('processid')->count() < $limit){
			$item = Queue_item::whereNull('processid')::with('submission.problem', 'submission.user', 'submission.language')->first();
			if ($item != NULL){
				$item->processid = getmypid();
				$item->save();
			}
		}

		DB::commit();
		return $item;
	}

	public function save_and_remove(){
		$arr = array(
			'status' => $submission['status'],
			'pre_score' => $submission['pre_score'],
		);

		$final_sub = $this->submit_model->get_final_submission(
			$submission['username'], $submission['assignment_id'], $submission['problem_id']
		);

		if (
			$final_sub == NULL 
			|| 
			(	$final_sub->pre_score < $submission['pre_score']
				|| $final_sub->pre_score * $final_sub->coefficient < $submission['pre_score'] * $submission['coefficient']
			)
		){
			$this->db->where(array(
				'is_final' => 1,
				'username' => $submission['username'],
				'assignment_id' => $submission['assignment_id'],
				'problem_id' => $submission['problem_id'],
			))->update('submissions', array('is_final'=>0));

			$arr['is_final'] = 1;
		}

		$this->db->where(array(
			'submit_id' => $submission['submit_id'],
			'username' => $submission['username'],
			'assignment_id' => $submission['assignment_id'],
			'problem_id' => $submission['problem_id']
		))->update('submissions', $arr);

		// update scoreboard:
		$this->load->model('scoreboard_model');
		$this->scoreboard_model->update_scoreboard($submission['assignment_id']);
	}
}
