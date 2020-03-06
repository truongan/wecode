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

}
