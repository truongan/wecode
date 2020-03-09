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
		
		$submission = $this->submission;

		$final_sub = Submission::where([
			'user_id' => $submission->user_id,
			'assignment_id' => $submission->assignment_id,
			'problem_id' => $submission->problem_id,
			'is_final' => 1
		])->first();



		if (
			$final_sub == NULL 
			|| 
			(	$final_sub->pre_score < $submission['pre_score']
				|| $final_sub->pre_score * $final_sub->coefficient < $submission['pre_score'] * $submission['coefficient']
			)
		){
			if ($final_sub){
				$final_sub->is_final = 0;
				$final_sub->save();
			}
			$submission->is_final = 1;
		}

		$submission->save();

		$this->delete();

		// update scoreboard:
		// $this->load->model('scoreboard_model');
		// $this->scoreboard_model->update_scoreboard($submission['assignment_id']);
	}
}
