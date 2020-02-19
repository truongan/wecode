<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Setting;
use App\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class assignment_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('assignments.list',['assignments'=> Assignment::all(), 'selected' => 'assignments']); 
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
            abort(404);
        return view('assignments.create',['problems' => Problem::all(), 'messages' => [], 'selected' => 'assignments']);
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
        $assignment=new Assignment;
        $assignment->name = $request->name;
        $assignment->description = $request->description;
        if ($request->open == 'on')
            $assignment->open = True;
        else $assignment->open = False;
        if ($request->score_board == 'on')
            $assignment->score_board = True;
        else $assignment->score_board = False;
        $assignment->extra_time = $request->extra_time;
        $start_time = strval($request->start_time_date) . " " . strval($request->start_time_time);
        $assignment->start_time = date('Y-m-d H:i:s', strtotime($start_time));
        $finish_time = strval($request->finish_time_date) . " " . strval($request->finish_time_time);
        $assignment->finish_time = date('Y-m-d H:i:s', strtotime($finish_time));
        $assignment->total_submits = 0;
        $assignment->javaexceptions=0;
        if ($request->late_rule!=NULL)
            $assignment->late_rule=$request->late_rule;
        else $assignment->late_rule="";
        $assignment->participants=$request->participants;
        $assignment->moss_update='';
        $assignment->save();
        if ($request->hasFile('pdf_file')) {
            $path_pdf = Setting::get("assignments_root");
            $path_pdf = $path_pdf . "/assignment_" .  strval($assignment->id);
            mkdir($path_pdf);
            $path = $request->pdf_file->storeAs($path_pdf,$request->pdf_file->getClientOriginalName(),'my_local');
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
        return view('assignments.create',['assignment' => $assignment, 'problems' => Problem::all(), 'messages' => [], 'selected' => 'assignments']);
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
        $assignment->name = $request->name;
         $assignment->description = $request->description;
        if ($request->open == 'on')
            $assignment->open = True;
        else $assignment->open = False;
        if ($request->score_board == 'on')
            $assignment->score_board = True;
        else $assignment->score_board = False;
        $assignment->extra_time = $request->extra_time;
        $start_time = strval($request->start_time_date) . " " . strval($request->start_time_time);
        $assignment->start_time = date('Y-m-d H:i:s', strtotime($start_time));
        $finish_time = strval($request->finish_time_date) . " " . strval($request->finish_time_time);
        $assignment->finish_time = date('Y-m-d H:i:s', strtotime($finish_time));
        $assignment->total_submits = 0;
        $assignment->javaexceptions=0;
        if ($request->late_rule!=NULL)
            $assignment->late_rule=$request->late_rule;
        else $assignment->late_rule="";
        $assignment->participants=$request->participants;
        $assignment->moss_update='';
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
