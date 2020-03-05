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
	
	public function show(Assignment $assignment, $problem_id ){
        $assignment_id = $assignment->id;

        if ($assignment_id > Assignment::count())
            abort(404);
        
        Auth::user()->selected_assignment_id = $assignment_id;
        Auth::user()->save(); 
        
        if ($problem_id == 0 )
        {
            $error = "no problem in the assignment";
            return view('problems.show',['error' => $error]);
        }
         
        if ($assignment_id === NULL){
            redirect(view('problems.show'));
        }
        
        $data=array(
            'can_submit' => TRUE,
            'problem_status' => NULL,
            'sum_score' => 0
        );
        
        $assignment = Assignment::find($assignment_id);
       
        $check = False;
        foreach($assignment->problems as $item)
        {
            if ($problem_id == $item->pivot->problem_id)
            {
                $check = True;
                break;
            }
        }
        if (!$check) abort(404);
        $result = $this->get_description($problem_id);
        $problem = Problem::find($problem_id);
        $problem['has_pdf'] = $result['has_pdf'];
        $problem['description'] = $result['description'];
        $problem['error'] = NULL;
        $data['problem'] = $problem;
        $data['language'] = $problem->languages(); 
        $data['all_problems'] = $assignment->problems;
        $data['assignment'] =$assignment;
        
        while(1){
            
            if($assignment->id == 0){
                if ( !in_array( Auth::user()->role->name, ['admin']) && $problem_id != 0) redirect('problems.show/'.$problem_id);
                $data['error'] = "There is nothing to submit to. Please select assignment and problem.";
                break;
            }
           
            if (! $assignment->started()){
				$data['error'] = "selected assignment hasn't started yet";
				break;
            }
      
            if ($assignment->open == 0  && in_array( Auth::user()->role->name, ['admin', 'head_instructor'])){
				$data['error'] =("assignment " . $assignment['id'] . " has ben closed");
				break;
            }
           
            if (! $assignment->is_participant(Auth::user()->id)){
				$data['error'] = "You are not registered to participate in this assignment";
				break;
            }
            // dd($assignment);
            $a = $assignment->can_submit(Auth::user());
            $data['can_submit'] = $a->can_submit;
            $data['sum_score'] = 0;
            foreach( $assignment->problems as $p)
            {
                $data['sum_score'] = $data['sum_score'] + $p->pivot->score;
            }
           
            $data['error'] = NULL;
           
            $probs = [];
           
            $subs = $assignment->submissions->where('is_final',1)->where('user_id',Auth::user()->id);
            
            foreach($assignment->problems as $p)
            {
                $probs[$p->pivot->id] = 'text-light bg-secondary';
            }

            foreach($subs as $sub){
				$class = "";
				if($sub->status != 'PENDING'){
					if ($sub->pre_score == 10000) $class = 'text-light bg-success';
					else $class = "text-light bg-danger";
				} else $class = "text-light bg-secondary";
				$probs[$sub['problem_id']] = $class;
            }
            
            $data['problem_status'] = $probs;
            break;
        }
        return view('problems.show',$data);
    }
}
