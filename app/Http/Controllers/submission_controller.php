<?php

namespace App\Http\Controllers;

use App\Submission;
use App\Assignment;
use App\Problem;
use App\Queue_item;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class submission_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($assignment_id = NULL, $user_id = 'all', $problem_id = 'all', $choose = 'all')
    {
        if ($assignment_id == 0)
            abort(403,'You have not selected assignment');
        Auth::user()->selected_assignment_id = $assignment_id;
        Auth::user()->save(); 
        if ( in_array( Auth::user()->role->name, ['student']) )
        {
            if ($choose == 'final')
                $submissions = Submission::where('assignment_id',$assignment_id)->where('user_id',Auth::user()->id)->where('is_final',1)->get();
            else
                $submissions = Submission::where('assignment_id',$assignment_id)->where('user_id',Auth::user()->id)->get();
            if ($problem_id != 'all')
                $submissions = collect($submissions->where('problem_id',intval($problem_id))->all());
            return view('submissions.list',['submissions' => $submissions, 'assignment_id' => $assignment_id, 'user_id' => $user_id, 'problem_id' => $problem_id, 'choose' => $choose, 'selected' => 'submissions']);
        }
        else 
        {
            if ($choose == 'final')
                $submissions = Submission::where('assignment_id',$assignment_id)->where('is_final',1)->get();
            else  
                $submissions = Submission::where('assignment_id',$assignment_id)->get();
            if ($user_id != 'all')
                $submissions = collect($submissions->where('user_id',intval($user_id))->all());
            if ($problem_id != 'all')
                $submissions = collect($submissions->where('problem_id',intval($problem_id))->all());
            return view('submissions.list',['submissions' => $submissions, 'assignment_id' => $assignment_id, 'user_id' => $user_id, 'problem_id' => $problem_id, 'choose' => $choose, 'selected' => 'submissions']); 
        }
    }

    public function create(Assignment $assignment, Problem $problem){
        return view('submissions.create', ['assignment' => $assignment, 'problem' => $problem]);
    }

    public function store($request)
    {
        $validated = $request->validate([
            'assignment' => ['integer', 'greater_than[-1]'],
            'problem' => ['integer', 'greater_than[0]'],
        ]);
        
        if (upload($request))
            return index($request->assignment);
        else
            abort(403,'Error Uploading File');
    }
    
    private function eval_coefficient($assignment)
    {
        ob_start();
        try 
        {
            eval($assignment->late_rule);
        }
        catch (\Throwable $e) 
        {
            $coefficient = "error";
        }
        if (!isset($coefficient))
            $coefficient = "error";
        ob_end_clean();
        return $coefficient;
    }
 
    public function upload_file_code($assignment, $problem, $user_dir, $submit_info)
    {

    }

    public function upload_post_code($assignment, $problem, $a, $user_dir, $submit_info)
    {

    }

    private function add_to_queue($submit_info, $assignment, $file_name)
    {
        $submit_info->submit_id = $assignment->increment('total_submits');
        $submit_info->file_name = $file_name;

        Queue_item::add_to_queue($submit_info);
        process_the_queue();
    }

    public function get_template($request){
        $validated = $request->validate([
            'assignment_id' => ['integer'],
            'problem_id' => 'integer',
        ]);
        
        $assignment = Assignment::find($assignment_id);
        if ($assignment == NULL || $assignment->can_submit(Auth::user()->id)){
            abort(403, 'Either assigment ID is invalid or you cannot submit to this assigment');
        }

        
    }

    public function upload($request)
    {
        $problem = Problem::where('id',$request->problem)->get();
        $assignment = Assignment::where('id',$request->assignment)->get();
        $language = Language::where('id',$request->language)->get();

        $coefficient = 100;
        if ($assignment->id == 0)
            if (!in_array( Auth::user()->role->name, ['admin']) && $problem->allow_practice!=1)
                abort(403,'Only admin can submit without assignment');
        else
        {
            $coefficient = eval_coefficient($assignment);

            $a = Assignment::can_submit($assignment);
            if(!$a->can_submit) abort(403, $a->error_message);

            if (Queue_item::in_queue(Auth::user()->id, $assignment->id, $problem->id))
                abort(403,'You have already submitted for this problem. Your last submission is still in queue.');

            
            if ($problem->languages->where('id',$language->id)->count() == 0)
                abort(403,'This file type is not allowed for this problem.');
        }

        $user_dir = Submission::get_path(Auth::user()->username, $assignment->id, $problem->id);

        if (!file_exists($user_dir))
            mkdir($user_dir, 0700, TRUE);

        $submit_info = (object)[
            'username' => Auth::user()->username,
            'assignment_id' => $assignment->id,
            'problem_id' => $problem->id,
            'language_id' => $language->id,
            'coefficient' => $coefficient,
            'pre_score' => 0,
            'time' => strtotime(date("Y-m-d H:i:s")),
        ];

        $a = $request->code;
        if ($a != NULL)
            return upload_post_code($assignment, $problem, $a, $user_dir, $submit_info);
        else 
            return upload_file_code($assignment, $problem, $user_dir, $submit_info);
    }
}
