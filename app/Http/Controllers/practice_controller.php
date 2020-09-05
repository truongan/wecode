<?php

namespace App\Http\Controllers;

use App\Problem;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class practice_controller extends Controller
{
	//
	public function __construct()
    {
        $this->middleware('auth'); // phải login
	}
	
    public function index()
    {
    	Auth::user()->selected_assignment_id = 0;
    	Auth::user()->save(); 
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
		if (!$problem){
			return view('problems.show',['error'=>'not found problem']);
		}
		if ($problem->allow_practice == 0)
		{
			return view('problems.show',['error'=>'the problem is not public']);
		}
		 
        $result = $this->get_description($problem_id);
        
        $problem = Problem::find($problem_id);
        $problem['has_pdf'] = $result['has_pdf'];
        $problem['description'] = $result['description'];
        return view('problems.show', ['problem'=>$problem,
                                      'all_problems'=>NULL,
                                      'can_submit'=>TRUE,
									  'assignment'=>NULL,
									  'selected' => "users"
                                      ]);    
	}

	public function get_description($id = NULL){
        $problem_dir = $this->get_directory_path($id);
        
		$result =  array(
			'description' => '<p>Description not found</p>',
			'has_pdf' => glob("$problem_dir/*.pdf") != FALSE,
			'has_template' => glob("$problem_dir/template.cpp") != FALSE
        );
		
		$path = "$problem_dir/desc.html";
        
		if (file_exists($path))
            $result['description'] = file_get_contents($path);   
       
		return $result;
	}

	public function get_directory_path($id = NULL){
        if ($id === NULL) return NULL;
        
		$assignments_root = Setting::get("assignments_root");
        
        $problem_dir = $assignments_root . "/problems/".$id;
       
        return $problem_dir;
	}
	
}
