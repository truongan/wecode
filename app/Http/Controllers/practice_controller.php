<?php

namespace App\Http\Controllers;

use App\Problem;
use Illuminate\Http\Request;

class practice_controller extends Controller
{
    //
    public function index()
    {
    	$problems = Problem::where('allow_practice',1)->get();
    	foreach ($problems as $problem)
    	{
    		$problem->total_submission = $problem->submissions->count();
    		$problem->accepted_submission = $problem->submissions->where('pre_score',10000)->count();
    		$problem->lang = $problem->languages;
    		$problem->tag = $problem->tags;
    	}
    	return view('practice',['problems' => $problems, 'selected' => 'practice']);
	}
	
	public function show($problem_id){
		$problem = Problem::find($problem_id);
		if ($problem)
		{
			
		}
	}
}
