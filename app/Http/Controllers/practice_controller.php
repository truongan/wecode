<?php

namespace App\Http\Controllers;

use App\Problem;
use App\Setting;
use App\Submission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class practice_controller extends Controller
{
	//
	public function __construct()
    {
        $this->middleware('auth'); // phải login
	}
	
    public function index()
    {
		// DB::enableQueryLog();
    	Auth::user()->selected_assignment_id = 0;
    	Auth::user()->save(); 
		$problems = Problem
			// ::with('submissions.assignment','languages')
			::with('languages')
			->where('allow_practice',1)
			->latest()
			->paginate(Setting::get('results_per_page_all'));


		$a  =  $problems->pluck('id');

        $total_subs = Submission::groupBy('problem_id')->where('assignment_id', 0)->whereIn('problem_id', $a)->select('problem_id', DB::raw('count(*) as total_sub'))->get()->keyBy('problem_id');
        $ac_subs = Submission::groupBy('problem_id')->where('assignment_id', 0)->whereIn('problem_id', $a)->where('pre_score', 10000)->select('problem_id', DB::raw('count(*) as total_sub'))->get()->keyBy('problem_id');
		// dd($a);
		foreach ($problems as $problem)
    	{
			// $all_submissions = $problem->submissions->filter(function($item,$key){return $item->assignment_id == 0;});
    		// $problem->total_submission = $all_submissions->count();
    		// $problem->accepted_submission = $all_submissions->filter(function($item,$key){return  $item->pre_score = 10000;})->count();
			
			// $problem->total_submission = $problem->submissions()->where(['assignment_id'=>0])->count();
			// $problem->accepted_submission = $problem->submissions()->where(['pre_score'=>10000, 'assignment_id'=>0])->count();
			// dd($total_subs[299]->total_sub?? 0);
            $problem->total_submission = $total_subs[$problem->id]->total_sub ?? 0;
            $problem->accepted_submission = $ac_subs[$problem->id]->total_sub ?? 0;
            $problem->ratio = round($problem->accepted_submit / max($problem->total_submit,1), 2)*100;
		}
		// dd(DB::getQueryLog());
    	return view('practice',['problems' => $problems, 'selected' => 'practice']);
	}
	
	public function show(Problem $problem){
		// $problem = Problem::find($problem_id);
		if (! $problem->can_practice(Auth::user()) )  {
			abort(403, 'This problem is not available for practice to you');
		}
		 
        $result = $problem->description();
        
        $problem = Problem::find($problem->id);
        $problem['has_pdf'] = $result['has_pdf'];
        $problem['description'] = $result['description'];
		$problem['has_template'] = $result['has_template'];
        $problem['error'] = NULL;
        return view('problems.show', ['problem'=>$problem,
                                      'all_problems'=>NULL,
                                      'can_submit'=>TRUE,
									  'assignment'=>NULL,
									  'selected' => "users"
                                      ]);    
	}

	public function get_directory_path($id = NULL){
        if ($id === NULL) return NULL;
        
		$assignments_root = Setting::get("assignments_root");
        
        $problem_dir = $assignments_root . "/problems/".$id;
       
        return $problem_dir;
	}
	
}
