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
		$submissions = $assignment->submissions()->with(['user', 'problem'])->where('is_final', 1)->get();
        $total_score = array();
		$total_accepted_score = array();
		$solved = array();
		$tried_to_solve = array();
		$penalty = array();
		$users = array();

        $scores = array();
        
        $problems = $assignment->problems->keyBy('id');
		$assignment_submissions = $assignment->submissions;

        $number_of_submissions= [];
		$number_of_submissions_during_freeze = [];

        foreach($assignment_submissions as $item)
        {
			$number_of_submissions[$item->user->username][$item->problem_id]=0;
            $number_of_submissions_during_freeze[$item->user->username][$item->problem_id]=0;
        }

		$lopsnames = array(); //Student in which class
		foreach ($assignment->lops()->with('users')->get() as $key =>$lop) {
			foreach ($lop->users as $key => $user) {
				$lopsnames[$user->username] = $lop->name;
			}
		}

        foreach($assignment_submissions as $item)
        {
			$first_ac = Submission::where([
				['assignment_id', $item->assignment_id], 
				['user_id', $item->user_id],
				['problem_id', $item->problem_id],
				['pre_score', 10000],
				['is_final', 1]])->first();

			if ($first_ac) {
				if ($item->created_at <= $first_ac->created_at) {
					$number_of_submissions[$item->user->username][$item->problem_id]+=1;
				}
			}
			else {
				$number_of_submissions[$item->user->username][$item->problem_id]+=1;
			}

			if($item->created_at >= $assignment->freeze_time) {
				$first_ac_before = Submission::where([
					['assignment_id', $item->assignment_id], 
					['user_id', $item->user_id],
					['problem_id', $item->problem_id],
					['pre_score', 10000],
					['is_final', 1],
					['created_at', '<', $assignment->freeze_time]])->first();
		
				if (!$first_ac_before) {
					if ($first_ac) {
						if ($item->created_at <= $first_ac->created_at) {
							$number_of_submissions_during_freeze[$item->user->username][$item->problem_id]+=1;
						}
					}
					else {
						$number_of_submissions_during_freeze[$item->user->username][$item->problem_id]+=1;
					}
				}
			}
		}

		
		$statistics = array();
        foreach($submissions as $submission)
        {
			$username = $submission->user->username;
			$problem_id = $submission->problem_id;
			$user_id = $submission->user->id;

			$pre_score = ceil($submission->pre_score * ($problems[$problem_id]->pivot->score ?? 0 ) / 10000);
			$final_score = ($submission['coefficient'] === 'error') ? 0 : ceil($pre_score * $submission['coefficient'] / 100);

			// dd($submission['created_at']);
			$fullmark = ($submission->pre_score == 10000);
			$time = CarbonInterval::seconds( $assignment->start_time->diffInSeconds($submission->created_at, true))->cascade(); // time is absolute different
			$late = CarbonInterval::seconds( $assignment->finish_time->diffInSeconds($submission->created_at, false))->cascade(); //late can either be negative (submit in time) or positive (submit late)
			$is_freeze = ($assignment->freeze_time <= $submission->created_at);

			// dd($late);
			$scores[$username][$problem_id] = [
				'score' => $final_score,
				'time' => $time,
				'late' => $late,
				'fullmark' => $fullmark,
				'is_freeze' => $is_freeze,
			];
			$scores[$username]['id'] = $user_id;

			$total_score[$username] = $total_score[$username] ?? 0;
			$total_accepted_score[$username] = $total_accepted_score[$username] ?? 0;
			$total_score_before_freeze[$username] = $total_score_before_freeze[$username] ?? 0;
			$total_accepted_score_before_freeze[$username] = $total_accepted_score_before_freeze[$username] ?? 0;

			$solved[$username] = $solved[$username] ?? 0;
			$tried_to_solve[$username] = $tried_to_solve[$username] ?? 0;
			$solved_before_freeze[$username] = $solved_before_freeze[$username] ?? 0;
			$penalty[$username] = $penalty[$username] ?? CarbonInterval::seconds(0);
			$penalty_before_freeze[$username] = $penalty_before_freeze[$username] ?? CarbonInterval::seconds(0);

			$solved[$username] += $fullmark;
			$tried_to_solve[$username] += 1;
			$total_score[$username] += $final_score;
			if ($fullmark) $total_accepted_score[$username] += $final_score;

			if($fullmark && $final_score > 0) {
				$compilation_error = Submission::where([
					['assignment_id', $assignment->id],
					['problem_id', $problem_id],
					['user_id', $user_id],
					['status', 'Compilation Error']
				])->count();
				$penalty[$username]->add($time->totalSeconds
					+ ($number_of_submissions[$username][$problem_id]-$compilation_error-1)
						* Setting::get('submit_penalty'), 'seconds');
			}

			if ($is_freeze) {
				$prescore_before_freeze = Submission::where([
					['assignment_id', $assignment->id],
					['created_at', '<', $assignment->freeze_time],
					['problem_id', $problem_id],
					['user_id', $user_id]
				])->max('pre_score');
				$pre_score = ceil($prescore_before_freeze * ($problems[$problem_id]->pivot->score ?? 0 ) / 10000);
				$fullmark = ($prescore_before_freeze == 10000);
			}

			$solved_before_freeze[$username] += $fullmark;
			$total_score_before_freeze[$username] += $final_score;
			if ($fullmark) $total_accepted_score_before_freeze[$username] += $final_score;

			if($fullmark && $final_score > 0) {
				$compilation_error = Submission::where([
					['created_at', '<', $assignment->freeze_time],
					['assignment_id', $assignment->id],
					['problem_id', $problem_id],
					['user_id', $user_id],
					['status', 'Compilation Error']
				])->count();
				$penalty_before_freeze[$username]->add($time->totalSeconds
					+ ((int)$number_of_submissions[$username][$problem_id] - (int)$number_of_submissions_during_freeze[$username][$problem_id] - $compilation_error - 1)
						* Setting::get('submit_penalty'), 'seconds');
			}

			$users[] = $submission->user;
			
        }

		$scoreboardKeys = ['username', 'user_id', 'score', 'lops', 'accepted_score', 'submit_penalty', 'solved', 'tried_to_solve'];
		$scoreboard = array_fill_keys($scoreboardKeys, []);
		$scoreboard['lops'] = $lopsnames;
		
		$scoreboard_freeze = $scoreboard;
		
		$users = array_unique($users);

		foreach($users as $user){
			$username = $user->username;
			$scoreboard['username'][] = $username;
			$scoreboard['score'][] = $total_score[$username];
			$scoreboard['accepted_score'][] = $total_accepted_score[$username];
			$scoreboard['submit_penalty'][] = $penalty[$username];
			$scoreboard['solved'][] = $solved[$username];
			$scoreboard['tried_to_solve'][] = $tried_to_solve[$username];
		
			$scoreboard_freeze['username'][] = $username;
			$scoreboard_freeze['score'][] = $total_score_before_freeze[$username];
			$scoreboard_freeze['accepted_score'][] = $total_accepted_score_before_freeze[$username];
			$scoreboard_freeze['submit_penalty'][] = $penalty_before_freeze[$username];
			$scoreboard_freeze['solved'][] = $solved_before_freeze[$username];
			$scoreboard_freeze['tried_to_solve'][] = $tried_to_solve[$username];
		}

		
        array_multisort(
			$scoreboard['accepted_score'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['submit_penalty'], SORT_NATURAL, SORT_ASC,
			array_map(function($time){return $time->total('seconds');}, $scoreboard['submit_penalty']),
			$scoreboard['solved'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['score'], SORT_NUMERIC, SORT_DESC,
			$scoreboard['username'],
			$scoreboard['tried_to_solve'],
			$scoreboard['submit_penalty'], SORT_NATURAL
        );
		
        array_multisort(
			$scoreboard_freeze['accepted_score'], SORT_NUMERIC, SORT_DESC,
			//$scoreboard_freeze['submit_penalty'], SORT_NATURAL, SORT_ASC,
			array_map(function($time){return $time->total('seconds');}, $scoreboard_freeze['submit_penalty']),
			$scoreboard_freeze['solved'], SORT_NUMERIC, SORT_DESC,
			$scoreboard_freeze['score'], SORT_NUMERIC, SORT_DESC,
			$scoreboard_freeze['username'],
			$scoreboard_freeze['tried_to_solve'],
			$scoreboard_freeze['submit_penalty'], SORT_NATURAL
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

		$assignment = $this->assignment;

		if (!$assignment || $assignment->id == 0) return false;		

		list ($scores, $scoreboard, $number_of_submissions,$stat_print, $number_of_submissions_during_freeze, $scoreboard_freeze) = $this->_generate_scoreboard();
		$all_problems = $assignment->problems;

		$total_score = $all_problems->map(function($item) {
			return $item->pivot->score;
		})->sum();
	
		$names = User::all()->pluck('display_name', 'username');

		$data = [
			'assignment_id' => $assignment->id,
			'problems' => $all_problems,
			'total_score' => $total_score,
			'scores' => $scores,
			'scoreboard' => $scoreboard,
			'names' => $names,
			'stat_print' => $stat_print,
			'no_of_problems'=> $assignment->problems->count(),
			'number_of_submissions' => $number_of_submissions,
			'number_of_submissions_during_freeze' => $number_of_submissions_during_freeze,
			'is_freeze' => (Carbon::now() >= $assignment->freeze_time),
			'scoreboard_freeze' => $scoreboard_freeze,
		];
		
		$this->scoreboard = view('scoreboard_table', $data)->render();
		$this->scoreboard_freeze = view('scoreboard_table_freeze', $data)->render();


		$this->save();
		
		return true;
	}

	public static function update_scoreboard($assignment_id)
	{
		if ($assignment_id) {
			//We don't create scoreboard for practice assignment
			return Scoreboard::firstOrCreate(['assignment_id' => $assignment_id], ['scoreboard' => ""])->_update_scoreboard();     
		}
	}

}