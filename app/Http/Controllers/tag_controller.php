<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class tag_controller extends Controller
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
        return view('tags.show',['tag'=>Tag::all()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);
        Tag::create($request->input());
        return view('tags.show',['tag'=>Tag::all()]); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
            abort(404);
        if ($id!=NULL)
            Tag::destroy($id);
        return view('tags.show',['tag'=>Tag::all()]); 
    }
}