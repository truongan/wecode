<?php

namespace App\Http\Controllers;

use App\Problemtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class problem_tag_controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        return view('problemtags.show',['Problemtags'=>Problemtag::all()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Problemtag  $problemtag
     * @return \Illuminate\Http\Response
     */
    public function show(Problemtag $problemtag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Problemtag  $problemtag
     * @return \Illuminate\Http\Response
     */
    public function edit(Problemtag $problemtag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Problemtag  $problemtag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Problemtag $problemtag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Problemtag  $problemtag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Problemtag $problemtag)
    {
        //
    }
}
