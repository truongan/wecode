<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
use Illuminate\Validation\Rules\AfterOrEqualIfOtherFieldIsEqualRule;
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
        $problem->user = (object)['username'=>'dummy'];

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
        DB::enableQueryLog();
        //
        if (!in_array( Auth::user()->role->name, ['admin']) )
        {
            $lops_id =  Auth::user()->lops->pluck('id');
            // dd($lops_id->join(','));
            // $assignments = Auth::user()->lops()->with('assignments')->get()->pluck('assignments')->collapse()->keyBy('id')->sortByDesc('created_at');
            $assignments = 
                Assignment::where( function($query) use ($lops_id){
                    $query
                        ->whereHas('lops' , function( $q) use ($lops_id){  
                            $q->whereIn('lops.id', $lops_id);
                        })
                        ->orWhere('user_id', Auth::user()->id);
                });
            if (Auth::user()->role->name == 'student'){
                $assignments = $assignments->where(['open' => 1]);
            }
            $assignments = $assignments->latest()->get();
            // dd(DB::getQueryLog());
            // dd($assignments->count());
        }
        else $assignments = Assignment::with('problems','lops')->latest()->get();
        foreach ($assignments as &$assignment)
        {
            $delay = strtotime(date("Y-m-d H:i:s")) - strtotime($assignment->finish_time);
            $submit_time = strtotime(date("Y-m-d H:i:s")) - strtotime($assignment->start_time);

            $assignment->coefficient = $assignment->eval_coefficient();// $coefficient;
            $assignment->finished = $assignment->is_finished();
            $assignment->no_of_problems = $assignment->problems->count();
        }
        $a =  view('assignments.list',['assignments'=> $assignments]); 
        
        return $a;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->role->name == 'admin'){
            $all_lops = Lop::latest()->get();
        } else if (Auth::user()->role->name == 'head_instructor'){
            $all_lops = Auth::user()->lops->keyBy('id');
        }
        else abort(403,'You do not have permission to edit assignment');

        $problems[-1] = $this->dummy_problem();
        
        if(Auth::user()->role->name == 'admin') $allprob = Problem::withCount('assignments')->latest()->get();
        else $allprob = Problem::available(Auth::user()->id)->latest()->withCount('assignments')->get();

        return view('assignments.create',['all_problems' => $allprob, 'all_lops' =>$all_lops, 'extra_time'=>'0*60*60', 'lops' => [], 'messages' => [], 'problems' => $problems, 'selected' => 'assignments']);
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

        $zone = Carbon::now()->getTimezone();

        $request['start_time'] = (new Carbon($request['start_time_date'] . ' ' . $request['start_time_time'] . ' ' . Setting::get('timezone')))->setTimezone($zone);
        $request['freeze_time'] = (new Carbon($request['freeze_time_date'] . ' ' . $request['freeze_time_time'] . ' ' . Setting::get('timezone')))->setTimezone($zone);
        $request['finish_time'] = (new Carbon($request['finish_time_date'] . ' ' . $request['finish_time_time'] . ' ' . Setting::get('timezone')))->setTimezone($zone);
        $request['unfreeze_time'] = (new Carbon($request['unfreeze_time_date'] . ' ' . $request['unfreeze_time_time'] . ' ' . Setting::get('timezone')))->setTimezone($zone);

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
            'freeze_time_date' => 'nullable|date|before_or_equal:finish_time_date',
            'freeze_time_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('freeze_time_date') === $request->input('finish_time_date')
                        && $value > $request->input('finish_time_time')
                    ) {
                        $fail('ERROR: Freeze time must be less than or equal to finish time');
                    }
                },
            ],

            'unfreeze_time_date' => 'nullable|date|after_or_equal:freeze_time_date',
            'unfreeze_time_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('unfreeze_time_date') === $request->input('freeze_time_date')
                        && $value <= $request->input('freeze_time_time')
                    ) {
                        $fail('ERROR: Unfreeze time must be greater than freeze time');
                    }
                },
            ],
        ],[
            'freeze_time_date.before_or_equal' => 'ERROR: Freeze time must be less than finish time',
            'freeze_time_time.before' => 'ERROR: Freeze time must be less than finish time',
            'unfreeze_time_date.after_or_equal' => 'ERROR: UnFreeze time must be more than finish time'
        ]);
        
            


        
        $input = $request->input();
        // dd($input);
        $this->_process_form($input);
        
        $assignment = new Assignment;
        $assignment->fill($input);
        $assignment->user_id = Auth::user()->id;

        $assignment->save();
        if ($request->hasFile('pdf')) {
            $path_pdf = Setting::get("assignments_root");

            $path = $request->pdf->storeAs("/assignment_" .  strval($assignment->id),$request->pdf->getClientOriginalName(),'assignment_root');
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
        
        $data['all_problems'] = $assignment->problems;
        
        if ( $data['all_problems']->pluck('id')->contains($problem_id) == false){
            //If we can't found problem_id, view the first problem
            $problem_id = $data['all_problems']->first()->id ?? null;
            if ($problem_id == null) abort(403, 'No problem to show');
        }
        
        $problem = Problem::find($problem_id);
        $result = $problem->description();
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
           
            if (! $assignment->started() && in_array( Auth::user()->role->name, ['student']) ){
				$data['error'] = "selected assignment hasn't started yet";
				break;
            }
      
            if ($assignment->open == 0  && in_array( Auth::user()->role->name, ['student'])){
				$data['error'] =("assignment " . $assignment['name'] . " has been closed");
				break;
            }
           
            if (! $assignment->is_participant(Auth::user())){
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
    

    

    public function duplicate(Assignment $assignment){
        if (($t = $assignment->cannot_edit(Auth::user())) !== false){
            abort(403, $t);
        }

        $new = $assignment->replicate();
        $new->user_id = Auth::user()->id;
        $new->total_submits = 0;
        $new->save();

        foreach ($assignment->problems as $p) {
            $new->problems()->attach($p->id, ['score' => $p->pivot->score, 'problem_name' => $p->pivot->problem_name, 'ordering' =>$p->pivot->ordering] );
        }
        return redirect()->route('assignments.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Assignment $assignment)
    {
        
        if (($t = $assignment->cannot_edit(Auth::user())) !== false){
            abort(403, $t);
        }

        if (Auth::user()->role->name == 'admin'){
            $all_lops = Lop::latest()->get();
        } else {
            $all_lops = Auth::user()->lops->keyBy('id');
        }

        $problems = [];
        $problems = $assignment->problems()->orderBy('ordering')->get()->push($this->dummy_problem())->keyBy('id');

        $e = $assignment->extra_time;
        if ($e % 3600 == 0) $assignment->extra_time = intval($e/3600) . "*60*60";
        else if ($e % 60 == 0) $assignment->extra_time = intval($e/36) . "*60";

        $lops = $assignment->lops->keyBy('id');


        if(Auth::user()->role->name == 'admin') $allprob = Problem::withCount('assignments')->latest()->get();
        else $allprob = Problem::available(Auth::user()->id)->latest()->withCount('assignments')->get();

        return view('assignments.create',['assignment' => $assignment, 'all_problems' => $allprob, 'messages' => [], 'problems' => $problems, 'all_lops' => $all_lops, 'lops' => $lops, 'selected' => 'assignments']);
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
        if (($t = $assignment->cannot_edit(Auth::user())) !== false){
            abort(403, $t);
        }
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
            $path = $request->pdf->storeAs("/assignment_" .  strval($assignment->id),$request->pdf->getClientOriginalName(),'assignment_root');
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
        $assignment->update_submissions_coefficient();
        Scoreboard::update_scoreboard($assignment->id);

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
        if ($id == 0){
            //Do nothing, we just don't touch the practice assignment
        }
        else
        {
            //TO DO SOMETHING HERE
            if (Assignment::find($id) == null)
                $json_result = array('done' => 0, 'message' => 'Cannot delete assignment: "Not found"');
            else
            {
                $assignment = Assignment::find($id);

                if (($t = $assignment->cannot_edit(Auth::user())) !== false){
                    $json_result = ['done' => 0, 'message' => $t];
                } else {

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
        $ass = Assignment::find($assignment_id) ;
        if ($ass == null)
            abort(404);

        if ($ass->submissions->count() == 0) abort(404);
        
        $assignments_root = Setting::get("assignments_root");
        $zipFile = $assignments_root . "/assignment" . (string)$assignment_id . "." . (string)date('Y-m-d_H-i') . ".zip";
        $pathdir = $assignments_root . '/assignment_' . $assignment_id . "/";
        shell_exec("zip -r $zipFile $pathdir");
        return response()->download($zipFile)->deleteFileAfterSend();
    }

    public function download_submissions($type, $assignment_id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        if (Assignment::find($assignment_id) == null)
            abort(404);
        if ($type !== 'by_user' && $type !== 'by_problem')
            abort(404);

        $assignments_root = Setting::get("assignments_root");
        $final_subs = Submission::get_final_submissions($assignment_id);

        if($final_subs->count() <= 0) abort (404);

        $zip = new ZipArchive;
        if ($type === 'by_user')
            $zip_name = $assignments_root . "/assignment" . (string)$assignment_id . "_submissions_by_user_" . (string)date('Y-m-d_H-i') . ".zip";
        elseif ($type === 'by_problem') 
            $zip_name = $assignments_root . "/assignment" . (string)$assignment_id . "_submissions_by_problem_" . (string)date('Y-m-d_H-i') . ".zip";
        $zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // dd($zip);
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

        if ($zip->count() == 0){
            abort(404, "No submissions to download");
        }
        $zip->close();
        

        return response()->download($zip_name)->deleteFileAfterSend();
    }
    
    public function reload_scoreboard($assignment_id)
    {
		// DB::enableQueryLog();

        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(403);
        $assignment = Assignment::find($assignment_id);
        if ($assignment == null){
            abort(404);
        }

        // Reset all final submission choice to the best score
        $problem_score = $assignment->problems->pluck('pivot.score','id');
        $subs = $assignment->submissions()->oldest()->get()->keyBy('id');


        $final_subs = [];
        foreach ($subs as $sub){
            $key = $sub->user_id . "," . $sub->problem_id;
            $sub->is_final = 0;
            $change = true;
            if (isset($final_subs[$key])){
                $final = $subs[ $final_subs[$key] ];

                $final_score = ceil($final->pre_score * ($problem_score[$final->problem_id] ?? 0)/10000);
                $final_score = ceil($final_score * ($final->coefficient === 'error' ? 0 : $final->coefficient/100) );
                
                $sub_score = ceil($sub->pre_score * ($problem_score[$sub->problem_id] ?? 0)/10000);
                $sub_score = ceil($sub_score * ($sub->coefficient === 'error' ? 0 : $sub->coefficient/100) );
                
                if ($sub->pre_score == 10000){
                    if ($final->pre_score == 10000 && $sub_score <= $final_score) $change = false;
                } else {
                    if ($final->pre_score == 10000) $change = false;
                    else if ($sub_score <= $final_score) $change = false;
                }
                if ($change){
                    $final->is_final = 0;
                    $final->save();
                }
            }
            if ($change){
                $final_subs[$key] = $sub->id;
                $sub->is_final = 1;
            }
            $sub->save();
        }

        // dd(DB::getQueryLog());

        // DB::disableQueryLo();
        if (Scoreboard::update_scoreboard($assignment_id)){     
            return redirect()->back()->with('success', 'Reload Scoreboard sucecss');   
        }
    }
    public function check_open(Request $request)
    {
        $assignment_id = $request->assignment_id;
        $assignment = Assignment::find($assignment_id);
        if ($assignment != NULL){

            if (($t = $assignment->cannot_edit(Auth::user())) !== false){
                echo "error, " . $t;
                return;
            }
               

            $assignment->open=!$assignment->open;
            $assignment->save();
            echo "success";
                return;
        }
        else 
            echo "error";
    }
}
