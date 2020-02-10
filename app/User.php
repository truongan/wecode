<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use Hash;


class User extends Authenticatable
{
    use Notifiable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'display_name', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
	 * User ID to Username
	 *
	 * Converts user id to username (returns FALSE if user does not exist)
	 *
	 * @param $user_id
	 * @return bool
	 */
	public static function user_id_to_username($user_id)
	{
		if( ! is_numeric($user_id))
			return FALSE;
		$query = DB::table('users')->select('username')->where('id','=',$user_id)->get();;
		if ($query->all() == NULL)
			return FALSE;
        return $query->all()[0]->username;
	}

    public function role(){
        return $this->belongsTo('App\Role');
    }

    public static function have_user($user_name){
        $query = User::where('username','=',$user_name)->first();
        if ($query == FALSE) 
            return FALSE;
        if ($query->count() == 0)
            return FALSE;
        return FALSE;
    }

    public static function add_user($username, $email, $password, $role, $display_name="")
    {
        // if ( ! $this->validate($username, ['filename' => 'alpha_num']))
		// 	return 'Username may only contain alpha-numeric characters.';
		if (strlen($username) < 3 OR strlen($username) > 20 OR strlen($password) < 6 OR strlen($password) > 200)
			return 'Username or password length error.';
		if (User::have_user($username))
			return 'User with this username exists.';
		if (User::have_email($email))
			return 'User with this email exists.';
		if (strtolower($username) !== $username)
			return 'Username must be lowercase.';
		$roles = array('admin', 'head_instructor', 'instructor', 'student');
		if ( ! in_array($role, $roles))
			return 'Users role is not valid.';
		$user = [
			'username' => $username,
			'email' => $email,
			'password' => Hash::make($password),
			'role_id' => 1,
			'display_name' => $display_name
		];
		
        DB::table('users')->insert($user);
	
		return TRUE; //success
    }

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
    
    public static function add_users($text, $send_mail, $delay)
	{
        
        $lines = preg_split('/\r?\n|\n?\r/', $text);
        
        $users_ok = collect(['']);
		
		$users_error = collect(['']);
        
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

			$result = User::add_user($parts[0], $parts[1], $parts[2], $parts[3], $parts[4]);
			$a = array($parts[0], $parts[1], $parts[2], $parts[3], $parts[4]);
			if ($result === TRUE)
				$users_ok = collect($a);
			else
				$users_error =  collect($a);
		} // end of loop

		// if ($send_mail)
		// {
		// 	// sending usernames and passwords by email
		// 	$this->load->library('email');
		// 	$config = array(
		// 		'mailtype'  => 'html',
		// 		'charset'   => 'iso-8859-1'
		// 	);
		// 	/*
		// 	// You can use gmail's smtp server
		// 	$config = Array(
		// 		'protocol' => 'smtp',
		// 		'smtp_host' => 'ssl://smtp.googlemail.com',
		// 		'smtp_port' => 465,
		// 		'smtp_user' => 'example@gmail.com',
		// 		'smtp_pass' => 'your-gmail-password',
		// 		'mailtype'  => 'html',
		// 		'charset'   => 'iso-8859-1'
		// 	);
		// 	*/
		// 	$this->email->initialize($config);
		// 	$this->email->set_newline("\r\n");
		// 	$count_users = count($users_ok);
		// 	$counter = 0;
		// 	foreach ($users_ok as $user)
		// 	{
		// 		$counter++;
		// 		$this->email->from($this->settings_model->get_setting('mail_from'), $this->settings_model->get_setting('mail_from_name'));
		// 		$this->email->to($user[1]);
		// 		$this->email->subject('Sharif Judge Username and Password');
		// 		$text = $this->settings_model->get_setting('add_user_mail');
		// 		$text = str_replace('{SITE_NAME}', $this->settings_model->get_setting('site_name'), $text);
		// 		$text = str_replace('{SITE_URL}', base_url(), $text);
		// 		$text = str_replace('{ROLE}', $user[3], $text);
		// 		$text = str_replace('{USERNAME}', $user[0], $text);
		// 		$text = str_replace('{PASSWORD}', htmlspecialchars($user[2]), $text);
		// 		$text = str_replace('{LOGIN_URL}', base_url(), $text);
		// 		$this->email->message($text);
		// 		$this->email->send();
		// 		if ($counter < $count_users)
		// 			sleep($delay);
		// 	}
		// }
		//return array($users_ok,$users_error);
		// $users_ok = collect(['sentest', 'trankimsen16819982@gmail.com', '123456789', '1', 'tester']);
		//$users_ok = array();//k phải lỗi này
	
		return $users_ok;
	}
    
    function submissions()
    {
        return $this->hasMany('App\Submission');
    }

    public static function delete_user($user_id)
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

//---------------------------------------------------------------------------------------------------------
