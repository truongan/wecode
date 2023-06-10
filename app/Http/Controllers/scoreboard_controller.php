<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scoreboard;
use App\Assignment;
use App\User;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DOMDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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
		if (in_array( Auth::user()->role->name, ['student']) && $assignment->score_board == false)
		{
			//Student can only view scoreboard if allowed
			abort(404, "This assignment does not have scoreboard");
		}
		$scoreboard = NULL;
		if ($assignment)
		{
			// Scoreboard::update_scoreboard($assignment_id);
			$scoreboard = $this->get_scoreboard($assignment_id);

		return view('scoreboard', ['selected' => 'scoreboard',
									'place' => 'full',	
									'assignment' => $assignment,
									'scoreboard' => $scoreboard,	
								]);
		}
	}
		
	public static function get_scoreboard($assignment_id)
	{
		$query =  DB::table('scoreboards')->where('assignment_id',$assignment_id)->get();
		
		if ($query->count() != 1)
			return false;//$message = array('error' => 'Scoreboard not found');
		else
		{
			return $query->first()->scoreboard;
		}
	}

	public function index_freeze($assignment_id)
	{
		$query =  DB::table('scoreboards')->where('assignment_id',$assignment_id)->get();

		$assignment = Assignment::find($assignment_id);
		// if (in_array( Auth::user()->role->name, ['student']) && $assignment->score_board == false)
		// {
		// 	//Student can only view scoreboard if allowed
		// 	abort(404, "This assignment does not have scoreboard");
		// }
		$scoreboard = NULL;
		$scoreboard_freeze = NULL;
		if ($assignment)
		{
			//////////////Scoreboard::update_scoreboard($assignment_id); AN Note: how can you do this, OMG 

			if ($query->count() != 1)
				$scoreboard_freeze = false;//$message = array('error' => 'Scoreboard not found');
			else
			{
				$scoreboard = $query->first()->scoreboard;
				$scoreboard_freeze = $query->first()->scoreboard_freeze;
			}

		return view('scoreboard_freeze', ['selected' => 'freeze',
									'place' => 'full',	
									'assignment' => $assignment,
									'scoreboard' => $scoreboard,
									'scoreboard_freeze' => $scoreboard_freeze,									
								]);
		}

	}

	public function get_scoreboard_freeze($assignment_id)
	{
		$query =  Scoreboard::where('assignment_id',$assignment_id)->get();

		if ($query->count() != 1)
			return false;
		else
		{
			return $query->first()->scoreboard_freeze;
		}
	}

	public function json_get_the_last_team($assignment_id) {
		$scoreboard_of_that_assignment =  Scoreboard::where('assignment_id',$assignment_id)->get();

		if ($scoreboard_of_that_assignment->count() != 1) {
			return abort(404, "This assignment does not have scoreboard");
		}
		else {
			$data = json_decode($scoreboard_of_that_assignment->first()->data, true);

			$scoreboard = $this->get_scoreboard($assignment_id);
			$last_team = $last_prob = $ordering = $temp = NULL;
			$all_usernames_desc = array_reverse($data['scoreboard_freeze']['username']);
			foreach($all_usernames_desc as $username) {
				if (array_sum($data['number_of_submissions_during_freeze'][$username])) {
					$last_team = $username;
					foreach(array_keys($data['number_of_submissions_during_freeze'][$username]) as $prob) {
						if ($data['number_of_submissions_during_freeze'][$username][$prob]) {
							$last_prob = $prob;
							$temp = DB::table('assignment_problem')
								->select('ordering')
								->where('assignment_id', $assignment_id)
								->where('problem_id', $prob)
								->get();
							$ordering = $temp[0]->ordering + 1;
							$data['number_of_submissions_during_freeze'][$username][$prob] = 0;
							$serialized_data = json_encode($data);
							DB::table('scoreboards')->where('assignment_id', $assignment_id)->update(['data' => $serialized_data]);
							break;
						}
					}
					break;
				}
			}
			return response()->json(['lastTeam' => $last_team, 'lastProb' => $last_prob, 'ordering' => $ordering,'scoreboard' => $scoreboard]);
		}
		
	}

	private function _strip_scoreboard($assignment_id){
	
		$a = $this->get_scoreboard($assignment_id);

		$dom = new DOMDocument;
		@$dom->loadHTML('<?xml encoding="UTF-8">'. $a);
		$ps = $dom->getElementsByTagName('p');
		while($ps->length > 0){
			$ps[0]->parentNode->removeChild($ps[0]);
		}
		//Remove excess info
		// $a = preg_replace('/[0-9]+:[0-9]+(\*\*)?/', '', $a);
		// $a = preg_replace('/\B-\B/', '', $a);
		// $a = preg_replace('/[0-9]+\*/', '0', $a);
		// $a = preg_replace('/\n+/', "\n", $a);
		// $a = preg_replace('/<p class="excess">.*<\/p>/', "", $a);

		//Remove the legend
		// $c = 0;
		// $i = strlen($a) - 1;
		// for(; $i > 0; $i--){
		//     if($a[$i] == "\n") $c++;
		//     if($c == 3) break;
		// }
		// $a = substr($a, 0, $i);

		return $dom->saveXML($dom->getElementsByTagName('table')[0]);
	}

	
	public function plain($assignment_id){
		$assignment = Assignment::find($assignment_id);

		$data = array(
			'place' => 'plain',
			'assignment' => $assignment,
			'scoreboard' => strip_tags( $this->_strip_scoreboard($assignment_id), "<table><thead><th><tbody><tr><td><br><tfoot>"),
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
    

}
