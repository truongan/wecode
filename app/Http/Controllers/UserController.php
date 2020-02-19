<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
        //
        $validated = $request->validate([
            'display_name' => ['required','max:50'],
            'email'=>['required'],
        ]);

        $user->display_name=$request->display_name;
        if ($request->password!="")
            $user->password=Hash::make($request->password);
        if ($request->role_id!=NULL)
            $user->role_id = $request->role_id;
        $user->save();
        if (Auth::user()->role->name=="admin")
            return redirect('users');
        return redirect('/home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
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
    // check same user 
    public function have_user($user_name){
        $query = User::where('username','=',$user_name)->first();
        if ($query == FALSE) 
            return FALSE;
        if ($query->count() == 0)
			return FALSE;
        return TRUE;
    }

    // check duplication display name

    public function duplication_display_name($display_name){
		$query = User::where('display_name','=',$display_name)->first();
		if ($query)
			return TRUE;
		return FALSE;
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
		$name = ['username'=>$username];
		$validator = Validator::make($name, [
            'username' => ['alpha_dash'],
		]);
		if ($validator->fails()) {
			array_push($json,'Username may only contain alpha-numeric characters.');
        }
		
		if (strlen($username) < 3 OR strlen($username) > 20 OR strlen($password) < 6 OR strlen($password) > 200)
            array_push($json,'Username or password length error.');
		
		if ($this->have_user($username))
		    array_push($json,'User with this username exists.');
        
        $mail = ['email'=>$email];
        $validator_mail = Validator::make($mail, [
                'email' => ['email'],
        ]);

        if ($validator_mail->fails())
            array_push($json,'error address email');

        if ($this->have_email($email))
            array_push($json,'User with this email exists.');
            
        if (strtolower($username) !== $username)
            array_push($json,'Username must be lowercase.');

        if ( ! in_array($role, ['1', '2', '3', '4']))
            array_push($json,'Users role is not valid.');
			
        if ($this->duplication_display_name($display_name))
            array_push($json,'User with this display_name exists.');
        
        if (count($json)>0)
            return $json;
        
		$user = [
			'username' => $username,
			'email' => $email,
			'password' => Hash::make($password),
			'role_id' => $role,
			'display_name' => $display_name
		];
        
		DB::table('users')->insert($user);
	
		return TRUE; //success
    }

    // add multiple user 
    public function add_users($text, $send_mail, $delay)
	{

        $lines = preg_split('/\r?\n|\n?\r/', $text);
        
        $users_ok = [];
		
		$users_error = [];
        
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
					
					$parts[2] = str_random($len);
				}
			}

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
    
    public function delete_user($user_id)
	{
        $user_count = DB::table('users')->count();
		if ($user_id == 1){
			///Cannot delete the fist user (usually the one who installed the Judge)
			///First user will only be deleted from database
			return false;
        } 
        else if ($user_count < 2){
			///Cannot delete the only user there is.
			return false;
        }
		DB::beginTransaction();
        try {
            $username = User::user_id_to_username($user_id);
            if ($username === FALSE)
                return FALSE;
            DB::table('users')->delete($user_id);
            // DB::delete('submissions', array('username' => $username));
            // each time we delete a user, we should update all scoreboards
            // $this->load->model('scoreboard_model');
            // $this->scoreboard_model->update_scoreboards();
            // shell_exec("cd {$this->settings_model->get_setting('assignments_root')}; rm -r */*/{$username};");
            DB::commit();
			return TRUE; //success
        } catch (Exception $e) {
            DB::rollBack();
            
            throw new Exception($e->getMessage());
            return FALSE; //success
        }
	}
}
