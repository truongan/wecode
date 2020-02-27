<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Setting;
use App\Problem;
use App\Lop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            #'pdf_file' => 'mimes:pdf',
        ]);
        
        $assignment = new Assignment;
        $assignment->fill($request->input());
        
        if ($request->open == 'on')
            $assignment->open = True;
        else $assignment->open = False;
        if ($request->score_board == 'on')
            $assignment->score_board = True;
        else $assignment->score_board = False;
        
        $assignment->start_time = date('Y-m-d H:i:s', strtotime($request->start_time));
        $assignment->finish_time = date('Y-m-d H:i:s', strtotime($request->finish_time));

        $assignment->save();
        if ($request->hasFile('pdf_file')) {
            $path_pdf = Setting::get("assignments_root");
            $path_pdf = $path_pdf . "/assignment_" .  strval($assignment->id);
            mkdir($path_pdf);
            $path = $request->pdf_file->storeAs($path_pdf,$request->pdf_file->getClientOriginalName(),'my_local');
        }
        foreach ($request->problem_id as $i => $id)
        {
            if ($id == -1) continue;
            $assignment->problems()->attach([
                $id => ['problem_name' => $request->problem_name[$i], 'score' => $request->problem_score[$i], 'ordering' => $i],
            ]);
        }

        foreach ($request->lop_id as $i => $id)
        {
            $assignment->lops()->attach($id);
        }

        return redirect('assignments');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        $b = $assignment->lops;
        foreach ($b as $i){
            $lops[$i->id] = $i;
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
            #'pdf_file' => 'mimes:pdf',
        ]);

        $assignment->fill($request->input());

        if ($request->open == 'on')
            $assignment->open = True;
        else $assignment->open = False;
        if ($request->score_board == 'on')
            $assignment->score_board = True;
        else $assignment->score_board = False;
       
        $start_time = strval($request->start_time_date) . " " . strval($request->start_time_time);
        $assignment->start_time = date('Y-m-d H:i:s', strtotime($start_time));
        $finish_time = strval($request->finish_time_date) . " " . strval($request->finish_time_time);
        $assignment->finish_time = date('Y-m-d H:i:s', strtotime($finish_time));
        $assignment->total_submits = 0;

        $assignment->save();
        if ($request->hasFile('pdf_file')) {
            $path_pdf = Setting::get("assignments_root");
            $path_pdf = $path_pdf . "/assignment_" .  strval($assignment->id);
            if (!file_exists($path_pdf)) {
                mkdir($path_pdf);
            }
            foreach(glob($path_pdf . "/*") as $file)
            {
                unlink($file);
            }
            $path = $request->pdf_file->storeAs($path_pdf,$request->pdf_file->getClientOriginalName(),'my_local');
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
        foreach ($request->lop_id as $i => $id)
        {
            $assignment->lops()->attach($id);
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
    }
}
