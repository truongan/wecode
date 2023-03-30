<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Scoreboard extends Model
{
	protected $fillable = ['assignment_id', 'scoreboard','scoreboard_freeze','users_ranking'];

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
			$users[] = $submission->user;

        }

		
		$this->users_ranking = $users;
		$this->save();

        $scoreboard = array(
			'username' => array(),
			'user_id' => array(),
			'score' => array(),
			'lops' => $lopsnames,
			'accepted_score' => array(),
			'submit_penalty' => array()
			,'solved' => array()
			,'tried_to_solve' => array()
        );
		
        $users = array_unique($users);
		foreach($users as $user){
			array_push($scoreboard['username'], $user->username);
			array_push($scoreboard['score'], $total_score[$user->username]);
			array_push($scoreboard['accepted_score'], $total_accepted_score[$user->username]);
			array_push($scoreboard['submit_penalty'], $penalty[$user->username]);
			array_push($scoreboard['solved'], $solved[$user->username]);
			array_push($scoreboard['tried_to_solve'], $tried_to_solve[$user->username]);
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
		// DB::enableQueryLog();
		$aggr = $assignment->submissions()->groupBy('user_id', 'problem_id')->select(DB::raw('user_id, problem_id, count(*) as submit'))->get();
		$aggr_ac = $assignment->submissions()->groupBy('user_id', 'problem_id')->where('pre_score', 10000)->select(DB::raw('user_id, problem_id, count(*) as submit'))->get();
		// dd(DB::getQueryLog());
		// DB::disableQueryLog();

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
			return array($scores, $scoreboard, $number_of_submissions, $stat_print, $number_of_submissions_during_freeze);
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

		list ($scores, $scoreboard, $number_of_submissions,$stat_print, $number_of_submissions_during_freeze) = $this->_generate_scoreboard();
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
		);
		// dd($data);

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
