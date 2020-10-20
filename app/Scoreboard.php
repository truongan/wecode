<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterval;
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
		CarbonInterval::setCascadeFactors(['minute' => [60, 'seconds'], 'hour' => [60, 'minutes']]); //Cascade to hours only when display submit time and delay time
		
    	$assignment = $this->assignment;
        $submissions = $assignment->submissions->where('is_final',1);
        $total_score = array();
		$total_accepted_score = array();
		$solved = array();
		$tried_to_solve = array();
		$penalty = array();
		$users = array();

        $scores = array();
        
        $problems = $assignment->problems->keyBy('id');
        $number_of_submissions= [];
        foreach($assignment->submissions as $item)
        {
            $number_of_submissions[$item->user->username][$item->problem_id]=0;
        }

		
        foreach($assignment->submissions as $item)
        {
            $number_of_submissions[$item->user->username][$item->problem_id]+=1;
		}
		// dd($item);
        foreach($submissions as $submission)
        {

			$pre_score = ceil(
						$submission->pre_score*
						($problems[$submission->problem_id]->pivot->score ?? 0 )/10000
			);
			if ($submission['coefficient'] === 'error') $final_score = 0;
			else $final_score = ceil($pre_score*$submission['coefficient']/100);

			// dd($submission['created_at']);
			$fullmark = ($submission->pre_score == 10000);
			$time = CarbonInterval::seconds( $assignment->start_time->diffInSeconds($submission->created_at, true))->cascade(); // time is absolute different
			$late = CarbonInterval::seconds( $assignment->finish_time->diffInSeconds($submission->created_at, false))->cascade(); //late can either be negative (submit in time) or positive (submit late)
			// dd($late);
            $username = $submission->user->username;
			$scores[$username][$submission->problem_id]['score'] = $final_score;
			$scores[$username][$submission->problem_id]['time'] = $time;
			$scores[$username][$submission->problem_id]['late'] = $late;
			$scores[$username][$submission->problem_id]['fullmark'] = $fullmark;
			$scores[$username]['id'] = $submission->user_id;
			if ( ! isset($total_score[$username])){
				$total_score[$username] = 0;
				$total_accepted_score[$username] = 0;
			}
			if ( !isset($solved[$username])){
				$solved[$username] = 0;
				$tried_to_solve[$username] = 0;
			}
			if ( ! isset($penalty[$username]))
				$penalty[$username] = CarbonInterval::seconds(0);

			$solved[$username] += $fullmark;
			$tried_to_solve[$username] += 1;
			$total_score[$username] += $final_score;
			if ($fullmark) $total_accepted_score[$username] += $final_score;
			
			if($fullmark
				&& $final_score > 0 //Only count problem with larger than 0 score
			) {
				$penalty[$username]->add($time->totalSeconds
					+ ($number_of_submissions[$submission->user->username][$submission->problem_id]-1)
						* Setting::get('submit_penalty'), 'seconds');
			}
			$users[] = $username;
        }

        $scoreboard = array(
			'username' => array(),
			'user_id' => array(),
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
			$scoreboard['accepted_score'], SORT_NUMERIC, SORT_DESC,
			//$scoreboard['submit_penalty'], SORT_NATURAL, SORT_ASC,
			array_map(function($time){return $time->total('seconds');}, $scoreboard['submit_penalty']),
			$scoreboard['solved'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['score'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['username'],
			$scoreboard['tried_to_solve'],
			$scoreboard['submit_penalty'], SORT_NATURAL
        );
    
        return array($scores, $scoreboard, $number_of_submissions);
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

		list ($scores, $scoreboard, $number_of_submissions) = $this->_generate_scoreboard();
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
			'no_of_problems'=> $assignment->problems->count(),
			'number_of_submissions' => $number_of_submissions,
			'assignment_id' => $assignment->id
		);
		// dd($data);


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
