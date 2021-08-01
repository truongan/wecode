<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Submission;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use DateTimeZone;

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
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!in_array(Auth::user()->role->name, ['admin', 'head_instructor'])){
            abort(403);
        }
        return view('users.list',['users'=>User::all(), 'selected' => 'settings', 'timezone' => Setting::get('timezone')]); 
    }

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        if ( ! in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']))
            if (Auth::user()->id != $id)
                abort(403);
        $user = User::with('lops')->findOrFail($id);
        $subs = $user->submissions()->with('assignment', 'assignment.lops', 'assignment.problems')->get();

        $total = $subs->count();
        $problem_wise_stat = array();
        $total_accept = 0;
        $solved_problems = array();
        $ass = array();
        foreach ($subs as $sub){
            $t = $ass[$sub->assignment->id] ??= (object)null;
            $t->ass ??= $sub->assignment;
            $t->total ??= 0;
            $t->accept ??= 0;
            $t->score ??= 0;
            $t->ac_score ??= 0;
            $t->solved ??= 0;

            $problem_wise_stat[$sub->problem_id] ??= 0 ;
            $problem_wise_stat[$sub->problem_id]++ ;
            $t->total++;
            if ($sub->pre_score == '10000') {
                $total_accept++;
                $t->accept ++;
                $solved_problems[$sub->problem_id] = $problem_wise_stat[$sub->problem_id];
            }
            if ($sub->is_final){
                $probs = $sub->assignment->problems->keyBy('id');
                if (isset($probs[$sub->problem_id])){
                    $pre_score = ceil(
						$sub->pre_score*
						($probs[$sub->problem_id]->pivot->score ?? 0 )/10000
			        );

                    $score =  ceil($pre_score*($sub->coefficient == 'error' ?  0 : $sub->coefficient)/100);
                    $t->score += $score;
                    if ($sub->pre_score == '10000') {
                        $t->solved ++;
                        $t->ac_score += $score;
                    }
                }
            }
        }

        $hourly = $user->submissions()->selectRaw('hour(created_at) as hour, count(*) as count')->groupByRaw('hour(created_at)')->get();
        $offset = (timezone_open(Setting::get('timezone')))->getOffset(now())/3600;
        
        foreach ($hourly as $key => $value) {
            $value->hour = ($value->hour+$offset)%24;
        }
        // $offset = (int)($offset);
        // $s = $hourly->splice(24-$offset) ;
        // $hourly = $s->concat($hourly);

        return view('users.show'
            , ['user' => $user, 'ass' => $ass, 'stat' => array('total_sub' => $total, 'total_accept' => $total_accept, 'prob_wise' => $problem_wise_stat, 'solved_problems' => $solved_problems) 
                ,'heat_map_data' => $user->submissions()->selectRaw('date(created_at) as date, count(*) as count')->groupByRaw('date(created_at)')->get()
                ,'pre_score_data' => $user->submissions()->selectRaw('floor(pre_score/1000) as pre_score, count(*) as count')->groupByRaw('floor(pre_score/1000)')->get()
                // ,'heat_map_data' => $sub->selectRaw('date(created_at) as date, count(*) as count')->groupByRaw('date(created_at)')->get()
                ,'hourly_data' => $hourly
            ]
        );
    }

    public function rank(Request $request){
        $name_list = preg_split("/[\s,]+/",$request->get('names'));

        $users = User::with('lops')->whereIn('username', $name_list)->get();
        $subs = Submission::with('assignment', 'assignment.lops', 'assignment.user')->whereIn('user_id', $users->pluck('id'))->get();
        $stats = array();
        // dd($users);
        // dd($subs);

        foreach ($users as $user ) {
            $t = $stats[$user->id] = (object)null;
            $t->total ??= 0;
            $t->problem_wise_stat ??= array();
            $t->total_accept ??= 0;
            $t->solved_problems ??= array();
        }

        foreach ($subs as $sub){
            $t=$stats[$sub->user_id];

            $t->total++;

            $t->problem_wise_stat[$sub->problem_id] ??= 0 ;
            $t->problem_wise_stat[$sub->problem_id]++ ;

            if ($sub->pre_score == '10000') {
                $t->total_accept++;
                $t->solved_problems[$sub->problem_id] = $t->problem_wise_stat[$sub->problem_id];
            }
        }
        // dd($users);
        // dd($stats);
        return view('users.rank', ['users' => $users, 'stats' => $stats]);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->role->name != 'admin'){
            abort(403);
        }
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
        if(Auth::user()->role->name != 'admin'){
            abort(403);
        }
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
        if(Auth::user()->role->name != 'admin' && Auth::user()->id != $user->id){
            abort(403);
        }
        return view('users.edit', ['user'=>$user]);
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
        if (Auth::user()->role->name != 'admin' && Auth::user()->id != $user->id){
            //Non admin only can update their own user profile
            abort(403);
        }   
        $request->validate([
            'username' => ['string', 'max:50', 'unique:users'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable','string', 'min:8', 'confirmed'],
            'trial_time' => ['nullable','numeric'],
        ]);
        if (Auth::user()->id == $user->id ) 
        {
            // When changing oneself's password (ofc you have to be admin at thí point)
            $request->validate([
                'old_password' => ['required_with:password', function ($attribute, $value, $fail) use ($user) {
                    if (isset($value) && !\Hash::check($value, $user->password)) {
                        return $fail(__('The current password is incorrect.'));
                    }
                }],
            ]);
            $a = $request->input();
            if ( isset( $a['trial_time'] ) )
            {
                abort(403, 'Only site admin can update user trial time');
            }
        }


        $data = $request->input();
        if (!isset($data['password']))
        {
            unset($data['password']);
            unset($data['password_confirmation']);
        }
        else
            $data['password'] = Hash::make($data['password']);
        
        $user->fill($data);
        $user->save();
        
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(Auth::user()->role->name != 'admin'){
            abort(403);
        }
        $user_id = $request['user_id'];
		if ( ! is_numeric($user_id) )
			$json_result = array('done' => 0, 'message' => 'Input Error');
        else {
            $user = User::find($user_id);
            if ($user->submissions()->count() > 0){
                $json_result = array('done' => 0, 'message' => "You must delete users' submission before you can delete user.");
            }
            elseif (User::destroy($user_id))
                $json_result = array('done' => 1);
            else
                $json_result = array('done' => 0, 'message' => 'Deleting User Failed');

        }

		header('Content-Type: application/json; charset=utf-8');
		return ($json_result);
    }

    public function delete_submissions(User $user){
        $subs = $user->submissions;
        $i = 0;
        $json_result = array('done'=>0 , 'count' => 0);
        foreach ($subs as $sub) {
            var_dump($sub->directory());
            shell_exec("rm -rf " . $sub->directory());
            $sub->delete();
            $i++;
        }
        $json_result['done'] = 1;
        $json_result['count'] = $i;
        header('Content-Type: application/json; charset=utf-8');
        return $json_result;
    }

    public function add_multiple()
    {
        return view('users.add');
    }

    public function add(Request $request)
    {
        if ($request->has(['new_users'])) {
            $all = $this->add_users(
                $request['new_users'],
                $request['send_mail'],
				$request['delay']
            );
            $ok = $all['users_ok'];
            $error = $all['users_error'];
            return view('users.add_result', ['ok' => $ok,'error' => $error]);
        }
        else
            return view('users.add');
    }
    

    //check email 

    public static function have_email($email, $username = FALSE)
	{
        $query = User::where('email','=',$email)->get();

		if ($query->count() >= 1){
			if($username !== FALSE && $query->first()->username == $username)
				return FALSE;
			else
				return TRUE;
		}
		return FALSE;
    }

    // add one user 
    public  function add_user($username, $email, $password, $role, $display_name="")
    {
        $json = [];
        // $name = ['username'=>$username];
        $user = [
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'role_id' => $role,
			'display_name' => $display_name
		];
		$validator = Validator::make($user, [
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
		]);
		if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message){
                array_push($json, $message);
            }
        }
        
        if (count($json)>0)
            return $json;
        $user['password'] = Hash::make($password);

        
		User::create($user);
	
		return TRUE; //success
    }

    // add multiple user 
    public function add_users($text, $send_mail, $delay)
	{

        $lines = preg_split('/\r?\n|\n?\r/', $text);
        
        $users_ok = [];
		$users_error = [];
        
        $role_name_to_id = Role::all()->pluck('id', 'name');

		// loop over lines of $text :
		foreach ($lines as $line)
		{
			$line = trim($line);

			if (strlen($line) == 0 OR $line[0] == '#')
				continue; //ignore comments and empty lines

			$parts = preg_split('/\s*,\s*/', $line);
			if (count($parts) != 5)
				continue; //ignore lines that not contain 5 parts

			if (strtolower(substr($parts[2], 0, 6)) == 'random')
			{
				// generate random password
				$len = trim(substr($parts[2], 6), '[]');
				if (is_numeric($len)){
					
					$parts[2] = substr(md5(rand()),0,$len);
				}
			}
            $parts[3] = $role_name_to_id[$parts[3]];
            $result = $this->add_user($parts[0], $parts[1], $parts[2], $parts[3], $parts[4]);
            
			$infomation_user = array($parts[0], $parts[1], $parts[2], $parts[3], $parts[4]);
            
            if ($result === TRUE)
				array_push($users_ok,$infomation_user);
            else
            {
                array_push($infomation_user,$result);
                array_push($users_error,$infomation_user);
            }
        }
        // gửi mail thì đếu biết :3
		
		return ['users_ok'=>$users_ok,'users_error'=>$users_error];
    }
    
}
