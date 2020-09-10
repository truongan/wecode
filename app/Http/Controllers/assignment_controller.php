<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Setting;
use App\Problem;
use App\Lop;
use App\Language;
use App\Submission;
use App\Scoreboard;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class assignment_controller extends Controller
{

    protected static function dummy_problem(){
        $problem = new class{}; 
        $problem->pivot = new class{};
        $problem->id = -1; 
        $problem->name = 'dummy'; 
        $problem->pivot->problem_name = 'dummy'; 
        $problem->pivot->score=0;
        $problem->admin_note = 'dummy';

        return $problem;
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth'); // phải login
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Auth::user()->role->name == 'student')
        {
            $assignments = Auth::user()->available_assignments()->sortByDesc('created_at');
        }
        else $assignments = Assignment::latest()->get();
        foreach ($assignments as $assignment)
        {
            $extra_time = $assignment->extra_time;
            $delay = strtotime(date("Y-m-d H:i:s")) - strtotime($assignment->finish_time);
            $submit_time = strtotime(date("Y-m-d H:i:s")) - strtotime($assignment->start_time);
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
            $assignment->coefficient = $coefficient;
            $assignment->finished = ($assignment->start_time < $assignment->finish_time &&  $delay > $extra_time);
            $assignment->no_of_problems = $assignment->problems->count();
        }
        return view('assignments.list',['assignments'=> $assignments, 'selected' => 'assignments']); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if ( !in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403,'You do not have permission to add assignment');
        

        $problems[-1] = $this->dummy_problem();

        return view('assignments.create',['all_problems' => Problem::all(), 'all_lops' => Lop::all(), 'lops' => [], 'messages' => [], 'problems' => $problems, 'selected' => 'assignments']);
    }


    private function _process_form(&$request){
        if ($request['open']??0 == 1)
            $request['open'] = True;
        else $request['open'] = False;
        
        if ($request['scoreboard']??0 == 1)
            $request['score_board'] = True;
        else $request['score_board'] = False;

        $extra_time = 1;
        foreach( explode('*',$request['extra_time'] ) as $t){
            $extra_time *= $t;
        }
        $request['extra_time'] = $extra_time;

        $request['start_time'] = date('Y-m-d H:i:s', strtotime((string)$request['start_time_date'] . " " .(string)date('H:i:s', strtotime($request['start_time_time']))));
        $request['finish_time'] = date('Y-m-d H:i:s', strtotime((string)$request['finish_time_date'] . " " .(string)date('H:i:s', strtotime($request['finish_time_time']))));
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if ( !in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403,'You do not have permission to add assignment');

        $validated = $request->validate([
            'name' => ['required','max:150'],
            'pdf_file' => 'mimes:pdf',
        ]);
        
        $input = $request->input();
        // dd($input);
        $this->_process_form($input);
        
        $assignment = new Assignment;
        $assignment->fill($input);

  
        $assignment->save();
        if ($request->hasFile('pdf')) {
            $path_pdf = Setting::get("assignments_root");
            $path_pdf = $path_pdf . "/assignment_" .  strval($assignment->id);
            mkdir($path_pdf);
            $path = $request->pdf->storeAs($path_pdf,$request->pdf->getClientOriginalName(),'my_local');
        }
        foreach ($request->problem_id as $i => $id)
        {
            if ($id == -1) continue;
            $assignment->problems()->attach([
                $id => ['problem_name' => $request->problem_name[$i], 'score' => $request->problem_score[$i], 'ordering' => $i],
            ]);
        }

        if ($request->lop_id != NULL)
        {
            foreach ($request->lop_id as $i => $id)
            {
                $assignment->lops()->attach($id);
            }
        }
        

        return redirect('assignments');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment, $problem_id ){
        $assignment_id = $assignment->id;

        if ($assignment->id == 0){
            return redirect()->route('practice');
        }
        
        // if ($assignment_id === NULL){
        //     redirect(view('problems.show'));
        // }
        
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
        $problem['has_template'] = $result['has_template'];
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
      
            if ($assignment->open == 0  && in_array( Auth::user()->role->name, ['student'])){
				$data['error'] =("assignment " . $assignment['name'] . " has been closed");
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

        Auth::user()->selected_assignment_id = $assignment_id;
        Auth::user()->save(); 

        return view('problems.show',$data);
    }

    public function get_directory_path($id = NULL){
        if ($id === NULL) return NULL;
        
		$assignments_root = Setting::get("assignments_root");
        
        $problem_dir = $assignments_root . "/problems/".$id;
       
        return $problem_dir;
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
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Assignment $assignment)
    {
       //
        if ( !in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403,'You do not have permission to edit assignment');
        $problems = [];
        $a = $assignment->problems()->orderBy('ordering')->get()->push($this->dummy_problem());
        foreach($a as $i){
            $problems[$i->id] = $i;
        }

        $lops = array();
        $b = $assignment->lops;
        if ($b != NULL)
        {
            foreach ($b as $i){
                $lops[$i->id] = $i;
            }
        }
        return view('assignments.create',['assignment' => $assignment, 'all_problems' => Problem::all(), 'messages' => [], 'problems' => $problems, 'all_lops' => Lop::all(), 'lops' => $lops, 'selected' => 'assignments']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assignment $assignment)
    {
        //
        if ( !in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403,'You do not have permission to edit assignment');

        $validated = $request->validate([
            'name' => ['required','max:150'],
            'pdf' => 'mimes:pdf',
        ]);

        $input = $request->input();
        $this->_process_form($input);
        $assignment->fill($input);
        
        
        // $assignment->total_submits = 0;

        $assignment->save();
        if ($request->hasFile('pdf')) {
            $path_pdf = Setting::get("assignments_root");
            $path_pdf = $path_pdf . "/assignment_" .  strval($assignment->id);
            if (!file_exists($path_pdf)) {
                mkdir($path_pdf);
            }
            foreach(glob($path_pdf . "/*") as $file)
            {
                unlink($file);
            }
            $path = $request->pdf->storeAs($path_pdf,$request->pdf->getClientOriginalName(),'my_local');
        }

        $assignment->problems()->detach();
        foreach ($request->problem_id as $i => $id)
        {
            if ($id == -1) continue;
            $assignment->problems()->attach([
                $id => ['problem_name' => $request->problem_name[$i], 'score' => $request->problem_score[$i], 'ordering' => $i],
            ]);
        }

        $assignment->lops()->detach();
        if ($request->lop_id != NULL)
        {
            foreach ($request->lop_id as $i => $id)
            {
                $assignment->lops()->attach($id);
            }
        }

        return redirect('assignments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        elseif ($id == 0){
            
        }
        elseif ($id === NULL)
        {
            $json_result = array('done' => 0, 'message' => 'Input Error');
        }
        else
        {
           
            //TO DO SOMETHING HERE

            if (Assignment::find($id) == null)
                $json_result = array('done' => 0, 'message' => 'Not found detailed');
            else
            {
                $assignment = Assignment::find($id);
                $submissions_in_queue = Submission::Where(['assignment_id' => $id, 'status' => 'pending'])->pluck('id')->toArray();
                DB::table('queue_items')->whereIn('submission_id', $submissions_in_queue)->delete();
                DB::table('submissions')->where('assignment_id', '=', $id)->delete();
                DB::table('assignment_lop')->where('assignment_id', '=', $id)->delete();
                DB::table('assignment_problem')->where('assignment_id', '=', $id)->delete();
                $path_pdf = Setting::get('assignments_root') . "/assignment_" .  strval($assignment->id);
                if (file_exists($path_pdf)) {
                    shell_exec("rm -r -f $path_pdf");
                }
                Assignment::destroy($id);
                $json_result = array('done' => 1);
            }
        }
        
		header('Content-Type: application/json; charset=utf-8');  
        return ($json_result);
    }

    public function score_accepted()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        return view('assignments.score_accepted');
    }

    public function score_sum()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        return view('assignments.score_sum');
    }


    public function download_all_submissions($assignment_id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        if (Assignment::find($assignment_id) == null)
            abort(404);

        $assignments_root = Setting::get("assignments_root");
        $zipFile = $assignments_root . "/assignment" . (string)$assignment_id . "." . (string)date('Y-m-d_H-i') . ".zip";
        $pathdir = $assignments_root . '/assignment_' . $assignment_id . "/";
        shell_exec("zip -r $zipFile $pathdir");
        return response()->download($zipFile)->deleteFileAfterSend();
    }

    public function download_submissions($type, $assignment_id)
    {
        // if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
        //     abort(403);
        if (Assignment::find($assignment_id) == null)
            abort(404);
        if ($type !== 'by_user' && $type !== 'by_problem')
            abort(404);

        $assignments_root = Setting::get("assignments_root");
        $final_subs = Submission::get_final_submissions($assignment_id);

        $zip = new ZipArchive;
        if ($type === 'by_user')
            $zip_name = $assignments_root . "/assignment" . (string)$assignment_id . "_submissions_by_user_" . (string)date('Y-m-d_H-i') . ".zip";
        elseif ($type === 'by_problem') 
            $zip_name = $assignments_root . "/assignment" . (string)$assignment_id . "_submissions_by_problem_" . (string)date('Y-m-d_H-i') . ".zip";
        $zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($final_subs as $final_sub)
        {
            $file_path = Submission::get_path($final_sub->username, $assignment_id, $final_sub->problem_id) 
            . "/" . (string)$final_sub->file_name . "." .(string)Language::find($final_sub->language_id)->extension;
            if ( ! file_exists($file_path))
                continue;
            $file = file_get_contents($file_path);
            if ($type === 'by_user')
                $zip->addFromString("{$final_sub->username}/problem_{$final_sub->problem_id}." . (string)Language::find($final_sub->language_id)->extension, $file);
            elseif ($type === 'by_problem')
                $zip->addFromString("problem_{$final_sub->problem_id}/{$final_sub->username}." . (string)Language::find($final_sub->language_id)->extension, $file);

        }

        $zip->close();

        return response()->download($zip_name)->deleteFileAfterSend();
    }
    
    public function reload_scoreboard($assignment_id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        $assignment = Assignment::find($assignment_id);
        if ($assignment == null)
            abort(404);
        if (Scoreboard::update_scoreboard($assignment_id)){     
            return redirect()->back()->with('success', 'Reload Scoreboard sucecss');   
        }
    }
    public function check_open(Request $request)
    {
        $assignment_id = $request->assignment_id;
        $assignment = Assignment::find($assignment_id);
        if ($assignment != NULL){
            $assignment->open=!$assignment->open;
            $assignment->save();
            echo "success";
                return;
        }
        else 
            echo "error";
    }
}
