<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('auth'); // phải login
		// if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']) )
        //     abort(404);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('users.list',['users'=>User::all(), 'selected' => 'settings']); 
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
     * Show the form for creating adding multiple users 
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        if ($request->has(['new_users'])) {
            
            $all = User::add_users(
                $request['new_users'],
                $request['send_mail'],
				$request['delay']
            );
            $ok = $all['users_ok'];
            $error = $all['users_error'];
         
            return view('users.add_result', ['ok' => $ok,'error' => $error]);//, 'error' => $error]);
        }
        else
            // nếu k phải phuong thức add thì nó cứ để view add 
            return view('users.add');
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
        $user->password=Hash::make($request->password);
        $user->display_name=$request->username;
        $user->email=$request->email;
        if ($request->role_id!="")
            $user->role_id=$request->role_id;
        else $user->role_id=4;
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
            $user->password=Hash::make($request->password);
        $user->save();
        return redirect('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // if ( ! $this->input->is_ajax_request() ) show_404();
        $user_id = $request['user_id'];
		if ( ! is_numeric($user_id) )
			$json_result = array('done' => 0, 'message' => 'Input Error');
		elseif (User::delete_user($user_id))
			$json_result = array('done' => 1);
		else
			$json_result = array('done' => 0, 'message' => 'Deleting User Failed');

		header('Content-Type: application/json; charset=utf-8');
		return ($json_result);
    }

}
