<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Scoreboard extends Model
{
	protected $fillable = ['assignment_id', 'scoreboard','scoreboard_freeze', 'data'];

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

		$lopsnames = array(); //Student in which class
		foreach ($assignment->lops()->with('users')->get() as $key =>$lop) {
			foreach ($lop->users as $key => $user) {
				$lopsnames[$user->username] = $lop->name;
			}
		}

		
        foreach($assignment->submissions as $item)
        {
			$number_of_submissions[$item->user->username][$item->problem_id]+=1;
		}
		
		$number_of_submissions_during_freeze = [];
        foreach($assignment->submissions as $item)
        {
            $number_of_submissions_during_freeze[$item->user->username][$item->problem_id]=0;
        }

		foreach($assignment->submissions->where('created_at', '>=', $assignment->freeze_time) as $item)
        {
            $number_of_submissions_during_freeze[$item->user->username][$item->problem_id]+=1;
		}
		
		$statistics = array();
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
			$is_freeze = ($assignment->freeze_time <= $submission->created_at);

			// dd($late);
            $username = $submission->user->username;
			$scores[$username][$submission->problem_id]['score'] = $final_score;
			$scores[$username][$submission->problem_id]['time'] = $time;
			$scores[$username][$submission->problem_id]['late'] = $late;
			$scores[$username][$submission->problem_id]['fullmark'] = $fullmark;
			$scores[$username][$submission->problem_id]['is_freeze'] = $is_freeze;
			$scores[$username]['id'] = $submission->user_id;
			if ( ! isset($total_score[$username])){
				$total_score[$username] = 0;
				$total_accepted_score[$username] = 0;
			}
			// Log::info(! isset($total_score_before_freeze['user4']));
			if ( ! isset($total_score_before_freeze[$username])){
				$total_score_before_freeze[$username] = 0;
				$total_accepted_score_before_freeze[$username] = 0;
			}
			if ( !isset($solved[$username])){
				$solved[$username] = 0;
				$tried_to_solve[$username] = 0;

				$solved_before_freeze[$username] = 0;
			}
			if ( ! isset($penalty[$username]))
				$penalty[$username] = CarbonInterval::seconds(0);
			if ( ! isset($penalty_before_freeze[$username]))
				$penalty_before_freeze[$username] = CarbonInterval::seconds(0);

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

			// Log::info($submissions);
			if ($is_freeze) {
				$prescore_before_freeze = $assignment->submissions
					->where('created_at', '<', $assignment->freeze_time)
					->where('problem_id', $submission->problem_id)
					->where('user_id', $submission->user->id)
					->max('pre_score');
				$pre_score = ceil($prescore_before_freeze*($problems[$submission->problem_id]->pivot->score ?? 0 )/10000);
				if ($submission['coefficient'] === 'error') $final_score = 0;
				else $final_score = ceil($pre_score*$submission['coefficient']/100);
				
				$fullmark = ($prescore_before_freeze == 10000);

				$solved_before_freeze[$username] += $fullmark;
				$total_score_before_freeze[$username] += $final_score;
				if ($fullmark) $total_accepted_score_before_freeze[$username] += $final_score;
				if($fullmark
					&& $final_score > 0 //Only count problem with larger than 0 score
				) {
					$penalty_before_freeze[$username]->add($time->totalSeconds
						+ ((int)$number_of_submissions[$submission->user->username][$submission->problem_id] - (int)$number_of_submissions_during_freeze[$submission->user->username][$submission->problem_id] -1)
							* Setting::get('submit_penalty'), 'seconds');
						}
				}
			else {
				$solved_before_freeze[$username] += $fullmark;
				$total_score_before_freeze[$username] += $final_score;
				if ($fullmark) $total_accepted_score_before_freeze[$username] += $final_score;
				if($fullmark
					&& $final_score > 0 //Only count problem with larger than 0 score
				) {
					$penalty_before_freeze[$username]->add($time->totalSeconds
						+ ((int)$number_of_submissions[$submission->user->username][$submission->problem_id] - (int)$number_of_submissions_during_freeze[$submission->user->username][$submission->problem_id] -1)
							* Setting::get('submit_penalty'), 'seconds');
						}
			}

			$users[] = $submission->user;
			// Log::info($total_score_before_freeze);
			

        }

		$total_accepted_times = array();
		$total_accepted_times_before_freeze = array();
		foreach($users as $user) {
			$total_accepted_time = 0;
			$total_accepted_time_before_freeze = 0;
			foreach($problems as $problem) {
				$submission = Submission::where([
                    ['assignment_id', $assignment->id], 
                    ['is_final', 1], 
                    ['user_id', $user->id],
					['problem_id', $problem->id],
					['pre_score', 10000]])->get();
				$submission_before = Submission::where([
					['assignment_id', $assignment->id], 
					['is_final', 1], 
					['user_id', $user->id],
					['problem_id', $problem->id],
					['pre_score', 10000], 
					['created_at', '<', $assignment->freeze_time]])->get();
				if ($submission) {
					foreach ($submission as $item) {
						$time = $assignment->start_time->diffInMinutes($item['created_at'], true);

						$penalty_score = ($number_of_submissions[$user->username][$problem->id] - 1) * \App\Setting::get('submit_penalty');
						$time += $penalty_score;

						$total_accepted_time += $time;
					}
				}
				if ($submission_before) {
					foreach ($submission_before as $item) {
						$time = $assignment->start_time->diffInMinutes($item['created_at'], true);

						$penalty_score = ($number_of_submissions[$user->username][$problem->id] - $number_of_submissions_during_freeze[$user->username][$problem->id] - 1) * \App\Setting::get('submit_penalty');
						$time += $penalty_score;

						$total_accepted_time_before_freeze += $time;
					}
                }
			}
			$total_accepted_times[$user->username] = $total_accepted_time;
			$total_accepted_times_before_freeze[$user->username] = $total_accepted_time_before_freeze;
		}

        $scoreboard = array(
			'username' => array(),
			'user_id' => array(),
			'score' => array(),
			'lops' => $lopsnames,
			'accepted_score' => array(),
			'submit_penalty' => array()
			,'solved' => array()
			,'tried_to_solve' => array()
			,'accepted_time' => array()
        );

		$scoreboard_freeze = array(
			'username' => array(),
			'user_id' => array(),
			'score' => array(),
			'lops' => $lopsnames,
			'accepted_score' => array(),
			'submit_penalty' => array()
			,'solved' => array()
			,'tried_to_solve' => array()
			,'accepted_time' => array()
        );
		
        $users = array_unique($users);
		foreach($users as $user){
			array_push($scoreboard['username'], $user->username);
			array_push($scoreboard['score'], $total_score[$user->username]);
			array_push($scoreboard['accepted_score'], $total_accepted_score[$user->username]);
			array_push($scoreboard['submit_penalty'], $penalty[$user->username]);
			array_push($scoreboard['solved'], $solved[$user->username]);
			array_push($scoreboard['tried_to_solve'], $tried_to_solve[$user->username]);
			array_push($scoreboard['accepted_time'], $total_accepted_times[$user->username]);
		}
		
		
        // array_multisort(
		// 	$scoreboard['accepted_score'], SORT_NUMERIC, SORT_DESC,
		// 	//$scoreboard['submit_penalty'], SORT_NATURAL, SORT_ASC,
		// 	array_map(function($time){return $time->total('seconds');}, $scoreboard['submit_penalty']),
		// 	$scoreboard['solved'], SORT_NUMERIC, SORT_DESC,
		// 	$scoreboard['score'], SORT_NUMERIC, SORT_DESC,
		// 	$scoreboard['username'],
		// 	$scoreboard['tried_to_solve'],
		// 	$scoreboard['submit_penalty'], SORT_NATURAL
        // );

		array_multisort(
			$scoreboard['accepted_score'], SORT_NATURAL, SORT_DESC,
			$scoreboard['accepted_time'], SORT_NATURAL, SORT_ASC,
			//$scoreboard['submit_penalty'], SORT_NATURAL, SORT_ASC,
			$scoreboard['username'], SORT_NATURAL, SORT_ASC,
			array_map(function($time){return $time->total('seconds');}, $scoreboard['submit_penalty']),
			$scoreboard['solved'],
			$scoreboard['score'],
			$scoreboard['tried_to_solve'],
			$scoreboard['submit_penalty'],
		);

		foreach($users as $user){
			array_push($scoreboard_freeze['username'], $user->username);
			array_push($scoreboard_freeze['score'], $total_score_before_freeze[$user->username]); //
			array_push($scoreboard_freeze['accepted_score'], $total_accepted_score_before_freeze[$user->username]); //
			array_push($scoreboard_freeze['submit_penalty'], $penalty_before_freeze[$user->username]); //
			array_push($scoreboard_freeze['solved'], $solved_before_freeze[$user->username]); //
			array_push($scoreboard_freeze['tried_to_solve'], $tried_to_solve[$user->username]);
			array_push($scoreboard_freeze['accepted_time'], $total_accepted_times_before_freeze[$user->username]);
		}
		
		
        // array_multisort(
		// 	$scoreboard_freeze['accepted_score'], SORT_NUMERIC, SORT_DESC,
		// 	//$scoreboard_freeze['submit_penalty'], SORT_NATURAL, SORT_ASC,
		// 	array_map(function($time){return $time->total('seconds');}, $scoreboard_freeze['submit_penalty']),
		// 	$scoreboard_freeze['solved'], SORT_NUMERIC, SORT_DESC,
		// 	$scoreboard_freeze['score'], SORT_NUMERIC, SORT_DESC,
		// 	$scoreboard_freeze['username'],
		// 	$scoreboard_freeze['tried_to_solve'],
		// 	$scoreboard_freeze['submit_penalty'], SORT_NATURAL
        // );

		array_multisort(
			$scoreboard_freeze['accepted_score'], SORT_NATURAL, SORT_DESC,
			$scoreboard_freeze['accepted_time'], SORT_NATURAL, SORT_ASC,
			//$scoreboard_freeze['submit_penalty'], SORT_NATURAL, SORT_ASC,
			$scoreboard_freeze['username'], SORT_NATURAL, SORT_ASC,
			array_map(function($time){return $time->total('seconds');}, $scoreboard_freeze['submit_penalty']),
			$scoreboard_freeze['solved'],
			$scoreboard_freeze['score'],
			$scoreboard_freeze['tried_to_solve'],
			$scoreboard_freeze['submit_penalty'],
		);
		// DB::enableQueryLog();
		$aggr = $assignment->submissions()->groupBy('user_id', 'problem_id')->select(DB::raw('user_id, problem_id, count(*) as submit'))->get();
		$aggr_ac = $assignment->submissions()->groupBy('user_id', 'problem_id')->where('pre_score', 10000)->select(DB::raw('user_id, problem_id, count(*) as submit'))->get();
		// dd(DB::getQueryLog());
		// DB::disableQueryLog();
		// Log::info($scoreboard['score']);
		// Log::info($scoreboard_freeze['score']);

		foreach($problems as $id=>$p){
			$statistics[$id] ??= new class{};
			$a = & $statistics[$id];
			$a->tries = 0;
			$a->tries_user = 0;
			$a->solved = 0;
			$a->solved_user = 0;
		}

		foreach ($aggr as $ag ) {
			$statistics[$ag->problem_id] ??= new class{};
			$a = & $statistics[$ag->problem_id];
			$a->tries = ($a->tries ?? 0) + $ag->submit;
			$a->tries_user = ($a->tries_user ?? 0) + 1;
		}
		foreach ($aggr_ac as $ag ) {
			$a = & $statistics[$ag->problem_id];
			$a->solved = ($a->solved ?? 0) + $ag->submit;
			$a->solved_user = ($a->solved_user ?? 0) + 1;
		}
		
		$stat_print = array();
		foreach($problems as $id=>$p){
			$a = &$statistics[$id] ;
			$stat_print[$id] = new class{};
			$stat_print[$id]->solved_tries = "$a->solved / $a->tries " . ($a->tries == 0 ? "" : "(" . round($a->solved * 100/$a->tries, 2) . "%)" );
			$stat_print[$id]->solved_tries_users = "$a->solved_user / $a->tries_user " 
				. ($a->tries_user == 0 ? "" : "(" . round($a->solved_user * 100/$a->tries_user, 2) . "%)" )
				. (count($users) == 0 ? "" : "(" . round($a->solved_user * 100/count($users), 2) . "%)" );
				$stat_print[$id]->average_tries =  ($a->tries == 0 ? "" : round($a->tries /$a->tries_user, 1) );
				$stat_print[$id]->average_tries_2_solve =  ($a->solved == 0 ? "" : round($a->tries /$a->solved, 1) );
				
			}
			
			// dd($statistics);
			// dd($stat_print);
			return array($scores, $scoreboard, $number_of_submissions, $stat_print, $number_of_submissions_during_freeze, $scoreboard_freeze);
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

		list ($scores, $scoreboard, $number_of_submissions,$stat_print, $number_of_submissions_during_freeze, $scoreboard_freeze) = $this->_generate_scoreboard();
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
			'stat_print' => $stat_print,
			'no_of_problems'=> $assignment->problems->count(),
			'number_of_submissions' => $number_of_submissions,
			'number_of_submissions_during_freeze' => $number_of_submissions_during_freeze,
			'is_freeze' => (Carbon::now() >= $assignment->freeze_time),
			'scoreboard_freeze' => $scoreboard_freeze,
		);
		
		$scoreboard_table = view('scoreboard_table', $data)->render();
		$scoreboard_table_freeze = view('scoreboard_table_freeze', $data)->render();

		#Minify the scoreboard's html code
		// $scoreboard_table = $this->output->minify($scoreboard_table, 'text/html');
		$this->scoreboard = $scoreboard_table;
		$this->scoreboard_freeze = $scoreboard_table_freeze;

		$this->save();
		
		return true;
	}

	public static function update_scoreboard($assignment_id)
	{
		// dd(['assignment_id' => $assignment_id]);
		if ($assignment_id != 0) {
			//We don't create scoreboard for practice assignment
			return Scoreboard::firstOrCreate(['assignment_id' => $assignment_id], ['scoreboard' => ""])->_update_scoreboard();     
		}
	}

}
