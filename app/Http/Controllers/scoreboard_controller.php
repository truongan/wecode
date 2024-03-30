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

		if (!in_array(Auth::user()->role->name, ['admin', 'head_instructor'])) {
			abort(404, "You are not allowed to view this page");
		}

		if ($assignment) {
			$scoreboard = Scoreboard::where('assignment_id', $assignment_id)->first();
	
			return view('scoreboard', [
				'selected' => 'scoreboard',
				'place' => 'full',    
				'assignment' => $assignment,
				'scoreboard' => $scoreboard ? $scoreboard->scoreboard : false,    
			]);
		}

	}

	public function index_freeze($assignment_id)
	{
		$assignment = Assignment::find($assignment_id);

		if (Auth::user()->role->name === 'student' && !$assignment->score_board) {
			abort(404, "Scoreboard is not allowed to view for this assignment");
		}

		$scoreboard = Scoreboard::where('assignment_id', $assignment_id)->first();
	
		return view('scoreboard_freeze', [
			'selected' => 'freeze',
			'place' => 'full',    
			'assignment' => $assignment,
			'scoreboard' => $scoreboard ? $scoreboard->scoreboard : false,
			'scoreboard_freeze' => $scoreboard ? $scoreboard->scoreboard_freeze : false,                                    
		]);

	}
}
