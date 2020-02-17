<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Setting;
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
        return view('assignments.list',['assignments'=>Assignment::all(), 'selected' => 'assignments']); 
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
        return view('assignments.create',['selected' => 'assignments']);
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
        $assignment->open = $request->open;
        $assignment->score_board = $request->score_board;
        $assignment->extra_time = $request->extra_time;
        $start_time = strval($request->start_time_date) . " " . strval($request->start_time_time);
        $assignment->start_time = date('Y-m-d H:i:s', strtotime($start_time));
        $finish_time = strval($request->finish_time_date) . " " . strval($request->finish_time_time);
        $assignment->finish_time = date('Y-m-d H:i:s', strtotime($finish_time));
        $assignment->total_submits = 0;
        $assignment->open = 0;
        $assignment->score_board=0;
        $assignment->javaexceptions=0;
        $assignment->late_rule="";
        $assignment->participants=$request->participants;
        $assignment->moss_update='';
        $assignment->save();
        if ($request->pdf_file->isValid()) {
            $path_pdf = Setting::get("assignments_root");
            $path_pdf = $path_pdf . "/assignment_" .  strval($assignment->id);
            mkdir($path_pdf);
            $path = $request->pdf_file->storeAs($path_pdf,$request->pdf_file->getClientOriginalName(),'my_local');
            var_dump($path);
        }
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
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
