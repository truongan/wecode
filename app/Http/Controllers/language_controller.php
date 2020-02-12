<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class language_controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        return view('languages.list',['Language'=>Language::order_languages()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(404);
        return view('languages.create');
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
        Language::create($request->input());
        return view('languages.list',['Language'=>Language::all()]); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function show(Language $language)
    {
        return view('languages.list',['Language'=>Language::all()]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function edit(Language $language)
    {
        return view('languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        $language->name = $request->name;
        $language->extension=$request->extension;
        $language->default_time_limit=$request->default_time_limit;
        $language->sorting=$request->sorting;
        $language->save();
        return view('languages.list',['Language'=>Language::order_languages()]); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        var_dump($request);
        die();
        // if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
        //     abort(404);
        // if ($id!=NULL)
        //     Language::destroy($id);
        return view('problems.list',['problems'=>Language::order_languages()]);  
    }
    
}
