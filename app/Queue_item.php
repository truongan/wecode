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
		$this->db->trans_start(); // We use the queue table as a mutex, so this function must be atomic
		$result = NULL;
		// var_dump($limit);
		if ($this->db->where('process_id is not NULL')->get('queue')->num_rows() < $limit){
			//The number of item being process is below limit
			
			$query = $this->db->where('process_id is NULL')
						->order_by('id')->limit(1)->get('queue');
			if ($query->num_rows() == 1){
				//We found a new item to process
				//Mark it as being process
				$queue = $query->row_array();
				$queue['process_id'] = getmypid();
				$this->db->where('id', $queue['id'])->update('queue', array('process_id'=>$queue['process_id']));

				$result = $queue;
			}

		}
		var_dump($result);
		$this->db->trans_complete();
		return $result;
	}

}
