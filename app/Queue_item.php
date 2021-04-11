<?php

namespace App;
use App\Submission;
use App\Scoreboard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Queue_item extends Model
{
    //
    protected $fillable = ['submission_id', 'type', 'processid'];

    public function submission()
    {
        return $this->belongsTo('App\Submission');
    }


	public  static function work(){
		/* But this function here in case I need
		changing the command parameter
		*/
		$a = shell_exec('php ' . escapeshellarg(base_path() . '/artisan').  ' work_queue >/dev/null 2>/dev/null &');
		return $a;
	}

    /**
	 * If the number of queue item being processed is less than limit
	 * Returns the first item of the queue that are not being processed
	 */
	public static function acquire($limit)
	{
		DB::beginTransaction(); // We use the queue table as a mutex, so this function must be atomic
		$item = NULL;
		// dd(Queue_item::whereNotNull('processid')->count());
		if(Queue_item::whereNotNull('processid')->count() < $limit){
			$item = Queue_item::whereNull('processid')->with('submission.problem', 'submission.user', 'submission.language')->oldest()->lockForUpdate()->first();
			// dd($item);
			if ($item != NULL){
				$item->processid = getmypid();
				$item->save();
			}
		}

		DB::commit();
		return $item;
	}

	public static function add_and_process($submit_id, $type){
		Queue_item::create([
			'submission_id' => $submit_id,
			'type' => $type
		]);
		$a  = Queue_item::work();

		return $a;
	}
	public static function add_not_process($submit_id, $type){
		$a = Queue_item::create([
			'submission_id' => $submit_id,
			'type' => $type
		]);

		return $a;
	}
	public function save_and_remove(){
		$submission = $this->submission;
		DB::beginTransaction();

		$final_sub = Submission::where([
			'user_id' => $submission->user_id,
			'assignment_id' => $submission->assignment_id,
			'problem_id' => $submission->problem_id,
			'is_final' => 1
		])->lockForUpdate()->first();
		if (
			$final_sub == NULL 
			|| 
			(	$final_sub->pre_score < $submission['pre_score']
				|| $final_sub->pre_score * intval($final_sub->coefficient) < $submission['pre_score'] * intval($submission['coefficient'])
			)
		){
			if ($final_sub){
				$final_sub->is_final = 0;
				$final_sub->save();
			}
			$submission->is_final = 1;
		}

		$submission->save();
		DB::commit();
		$this->delete();

		Scoreboard::update_scoreboard($submission->assignment_id);
		// update scoreboard:
		// $this->load->model('scoreboard_model');
		// $this->scoreboard_model->update_scoreboard($submission['assignment_id']);
	}
}
