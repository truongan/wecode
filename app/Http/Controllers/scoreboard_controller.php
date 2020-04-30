<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scoreboard;
use App\Assignment;
use App\User;
use App\Setting;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Eloquent\Collection;

class scoreboard_controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($assignment_id)
    {
		$assignment = Assignment::find($assignment_id);
		$scoreboard = NULL;
		if ($assignment)
		{
			$this->update_scoreboard($assignment_id); 

			$scoreboard = $this->get_scoreboard($assignment_id);
		
		}
		return view('scoreboard', ['selected' => 'scoreboard',
									'place' => 'full',	
									'assignment' => $assignment,
									'scoreboard' => $scoreboard,					
								]);
	}
	
	public function get_scoreboard($assignment_id)
	{
		$query =  DB::table('scoreboards')->where('assigment',$assignment_id)->get();

		if ($query->count() != 1)
			return false;//$message = array('error' => 'Scoreboard not found');
		else
		{
			return $query->first()->scoreboard;
		}
	}

	private function _strip_scoreboard($assignment_id){
		$a = $this->get_scoreboard($assignment_id);

		//Remove excess info
		$a = preg_replace('/[0-9]+:[0-9]+(\*\*)?/', '', $a);
		$a = preg_replace('/\B-\B/', '', $a);
		$a = preg_replace('/[0-9]+\*/', '0', $a);
		$a = preg_replace('/\n+/', "\n", $a);

		//Remove the legend
		$c = 0;
		$i = strlen($a) - 1;
		for(; $i > 0; $i--){
		    if($a[$i] == "\n") $c++;
		    if($c == 3) break;
		}
		$a = substr($a, 0, $i);

		return $a;
	}
	
	public function plain($assignment_id){
		$assignment = Assignment::find($assignment_id);

		$data = array(
			'place' => 'plain',
			'assignment' => $assignment,
			'scoreboard' => strip_tags( $this->_strip_scoreboard($assignment_id), "<table><thead><th><tbody><tr><td><br>"),
			'selected' => 'scoreboard'
		);
		
		return view('scoreboard', $data);
	}

	public function simplify($assignment_id){
		$assignment = Assignment::find($assignment_id);

		$data = array(
			'place' => 'simplify',
			'assignment' => $assignment,
			'scoreboard' => $this->_strip_scoreboard($assignment_id),
			'selected' => 'scoreboard',
		);

		return view('scoreboard', $data);
	}



	private function _generate_scoreboard($assignment_id)
    {
        $assignment = Assignment::find($assignment_id);
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
					+ $number_of_submissions[$username][$submission['problem_id']]
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
    
    public function update_scoreboard($assignment_id)
	{

		if ($assignment_id == 1)
			return false;
		
		$assignment =Assignment::find($assignment_id);

		if (!$assignment)
		{
			return false;
		}

		list ($scores, $scoreboard) = $this->_generate_scoreboard($assignment_id);
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
			'assignment_id' => $assignment_id,
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

		DB::table('scoreboards')->updateOrInsert([
			'assigment'  => $assignment_id,
			'scoreboard' => $scoreboard_table,
		]);
		
		return true;
	}

    public function update_scoreboards()
	{
		$assignments = Assignment::all();
		foreach ($assignments as $assignment){
			$this->update_scoreboard($assignment['id']);
		}
	}
}
