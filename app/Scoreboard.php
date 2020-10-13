<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Scoreboard extends Model
{
	protected $fillable = ['assignment_id', 'scoreboard'];

	public function assignment(){
        return $this->belongsTo('App\Assignment');
	}

	private function _generate_scoreboard()
    {
    	$assignment = $this->assignment;
        $submissions = $assignment->submissions->where('is_final',1);
        $total_score = array();
		$total_accepted_score = array();
		$solved = array();
		$tried_to_solve = array();
		$penalty = array();
		$users = array();
		$start = strtotime($assignment['start_time']);
		$end = strtotime($assignment['finish_time']);
		$submit_penalty = Setting::find('submit_penalty');
        $scores = array();
        
        $problems = $assignment->problems;
        
        foreach($assignment->submissions as $item)
        {
            $number_of_submissions[$item->user_id][$item->problem_id]=0;
        }

		
        foreach($assignment->submissions as $item)
        {
            $number_of_submissions[$item->user_id][$item->problem_id]+=1;
		}
		// dd($submissions);
        foreach($submissions as $submission)
        {
            $pre_score = ceil($submission['pre_score']*($problems[$submission['problem_id']]['score'] ?? 0 )/10000);
			if ($submission['coefficient'] === 'error')
				$final_score = 0;
			else
				$final_score = ceil($pre_score*$submission['coefficient']/100);
			$fullmark = ($submission['pre_score'] == 10000);
			$delay = strtotime($submission['time'])-$start;
            $late = strtotime($submission['time'])-$end;
            $username = $submission->user->username;
			$scores[$username][$submission['problem_id']]['score'] = $final_score;
			$scores[$username][$submission['problem_id']]['time'] = $delay;
			$scores[$username][$submission['problem_id']]['late'] = $late;
			$scores[$username][$submission['problem_id']]['fullmark'] = $fullmark;

			if ( ! isset($total_score[$username])){
				$total_score[$username] = 0;
				$total_accepted_score[$username] = 0;
			}
			if ( !isset($solved[$username])){
				$solved[$username] = 0;
				$tried_to_solve[$username] = 0;
			}
			if ( ! isset($penalty[$username]))
				$penalty[$username] = 0;

			$solved[$username] += $fullmark;
			$tried_to_solve[$username] += 1;
			$total_score[$username] += $final_score;
			if ($fullmark) $total_accepted_score[$username] += $final_score;
			
			if($fullmark) $penalty[$username] += $delay 
					+ $number_of_submissions[$submission['user_id']][$submission['problem_id']]
						*$submit_penalty;
			$users[] = $username;
        }

        $scoreboard = array(
			'username' => array(),
			'score' => array(),
			'accepted_score' => array(),
			'submit_penalty' => array()
			,'solved' => array()
			,'tried_to_solve' => array()
        );
        
        $users = array_unique($users);
		foreach($users as $username){
			array_push($scoreboard['username'], $username);
			array_push($scoreboard['score'], $total_score[$username]);
			array_push($scoreboard['accepted_score'], $total_accepted_score[$username]);
			array_push($scoreboard['submit_penalty'], $penalty[$username]);
			array_push($scoreboard['solved'], $solved[$username]);
			array_push($scoreboard['tried_to_solve'], $tried_to_solve[$username]);
		}
		
		
        array_multisort(
			$scoreboard['solved'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['accepted_score'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['score'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['submit_penalty'], SORT_NUMERIC, SORT_ASC,
			$scoreboard['username']
			,$scoreboard['tried_to_solve']
        );
    
        return array($scores, $scoreboard);
    }

	public function _update_scoreboard()
	{

		if ($this->assignment->id == 0)
			return false;
		
		$assignment = $this->assignment;

		if (!$assignment)
		{
			return false;
		}

		list ($scores, $scoreboard) = $this->_generate_scoreboard();
		$all_problems = $assignment->problems;
		
		$total_score = 0;
		foreach($all_problems as $item)
			$total_score += $item->pivot->score;
	
		$all_name = User::all();
		foreach($all_name as $row)
		{
			$result[$row->username] = $row->display_name;
		}
		
		$data = array(
			'assignment_id' => $assignment->id,
			'problems' => $all_problems,
			'total_score' => $total_score,
			'scores' => $scores,
			'scoreboard' => $scoreboard,
			'names' => $result,
			'no_of_problems'=> $assignment->problems->count()
		);
		$scoreboard_table = view('scoreboard_table', $data)->render();
		
		#Minify the scoreboard's html code
		// $scoreboard_table = $this->output->minify($scoreboard_table, 'text/html');
		$this->scoreboard = $scoreboard_table;
		$this->save();
		
		return true;
	}

	public static function update_scoreboard($assignment_id)
	{
		// dd(['assignment_id' => $assignment_id]);
		return Scoreboard::firstOrCreate(['assignment_id' => $assignment_id], ['scoreboard' => ""])->_update_scoreboard();     
	}

}
