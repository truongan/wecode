<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Hash;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        //
        return view('users.list',['users'=>User::all()]); 
    }

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        return view('users.show', ['user' => User::findOrFail($id)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('users.create');
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
        $user=new User;
        $user->username=$request->username;
        $user->password=bcrypt($request->password);
        $user->display_name=$request->username;
        $user->email=$request->email;
        $user->role_id=4;
        $user->first_login_time=date('Y-m-d H:i:s');
        $user->last_login_time=date('Y-m-d H:i:s');
        $user->selected_assigment=0;
        $user->save();
        return redirect('users');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
        $user->username=$request->username;
        $user->display_name=$request->display_name;
        if ($request->password!="")
        {
            $user->password=Hash::make($request->password);
        }
        $user->save();
        return redirect('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        $user = User::find($user->id);
        $user->delete();
        return redirect('users');  
    }

}
