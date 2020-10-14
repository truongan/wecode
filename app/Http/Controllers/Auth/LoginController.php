<?php

namespace App\Http\Controllers\Auth;

use App\Setting;
use App\User;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        
    }



	protected function uit_ldap($user, $password) {
        //define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
        $ldap_host = 'ad.uit.edu.vn';
        $ldap_dn = 'DC=AD,DC=UIT,DC=EDU,DC=VN';

        $userinfo = array();

        //ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
        $ldap = ldap_connect($ldap_host);
        //ldap_set_option($ldap, LDAP_OPT_DEBUG_LEVEL, 7);

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        // var_dump($ldap);
        // verify user and password

        // debug($bind = @ldap_bind($ldap, $user . '@'.$ldap_host, $password));
        //echo "<br/>" . ldap_error($ldap);

        //echo "<br/>" . ldap_error($ldap);
        if($bind = @ldap_bind($ldap, $user . '@'.$ldap_host, $password)) {
            $filter = "(sAMAccountName=" . $user . ")";
            $attr = array("memberof","displayname","mail");
            //$attr = array();
            $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
            $entries = ldap_get_entries($ldap, $result);
            ldap_unbind($ldap);
            //debug($entries);
            if ($entries[0]) {
                $userinfo['masv'] = $user;
                $userinfo['hoten'] = $entries[0]['displayname'][0];
                $userinfo['email'] = $entries[0]['mail'][0];
                $userinfo['GV'] = TRUE;
                if(strpos($entries[0]['dn'],'OU=UIT') === FALSE) $userinfo['GV'] = FALSE;
            }
		}
        return $userinfo;
    }
	protected function ldap_authentication($username, $password){
        $ldap_user = $this->uit_ldap($username, $password);
        $user_id = null;
		if ($ldap_user){
			//ldap login successfully
            // $user_id = $this->user_model->username_to_user_id($ldap_user['masv']);
            $user = User::where(['username'=>$ldap_user['masv']])->first();
			if ( $user ){
                $user_id = $user->id;
                Auth::login($user);
                
                ///Super optional: reset display name after each login
                $user->display_name = $ldap_user['hoten'];
                $user->save;
				// $this->db->where('id', $user_id)->update('users', array('display_name' => $ldap_user['hoten']));
				///
				return true;
			}
			else {
				///Optional: create user if not present.
				// $this->user_model->add_user(
				// 	$ldap_user['masv'], $ldap_user['email'], shj_random_password(20)
				// 	, $ldap_user['GV']?'head_instructor' : 'student'
				// 	, $ldap_user['hoten']
				// );
			}
		}
		return ($ldap_user && $user_id);
	}

    public function Login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $success = false;
        if ( Auth::viaRemember() 
            || $this->ldap_authentication($credentials['username'], $credentials['password']) 
            || Auth::attempt($credentials, $request->input('remember') !== NULL) 
        )
        {

            if (Auth::user()->first_login_time == NULL) Auth::user()->first_login_time = now();
            else Auth::user()->last_login_time=now();
        
            Auth::user()->save();
            
            return redirect()->intended('home');
        } else {
            return back()->withInput()->withErrors([
                'username' => 'Either your username or password are incorrect.',
                'password' => 'Either your username or password are incorrect.',
            ]);;
        }
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect()->route('login');
    }
    
}
