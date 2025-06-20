<?php

namespace App\Http\Controllers\Auth;

use App\Models\Setting;
use App\Models\User;
use Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, 1);
        ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);

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
	protected function ldap_authentication($username, $password, $remember = false){
        $ldap_user = $this->uit_ldap($username, $password);
        $user_id = null;
		if ($ldap_user){
			//ldap login successfully
            // $user_id = $this->user_model->username_to_user_id($ldap_user['masv']);
            $user = User::where(['username'=>$ldap_user['masv']])->first();
			if ( $user ){
                $user_id = $user->id;
                Auth::login($user, $remember);
                
                ///Super optional: reset display name after each login
                $user->display_name = $ldap_user['hoten'];
                $user->save;
				// $this->db->where('id', $user_id)->update('users', array('display_name' => $ldap_user['hoten']));
				///
				return true;
			}
			else {
				///Optional: create ldap user if not present.
                // $user = User::create([
                //     'username' => $ldap_user['masv'],
                //     'email' => $ldap_user['email'],
                //     'display_name' => $ldap_user['hoten'],
                //     'password' => Hash::make(Str::random(80)),
                //     'role_id' => $ldap_user['GV']?'3' : '4' // Trợ giảng hoặc sinh viên thôi.
                // ]);
                // Auth::login($user, $remember);
                // return true;
			}
		}
		return ($ldap_user && $user_id);
	}

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $success = false;
        if ( Auth::viaRemember() 
            || $this->ldap_authentication($credentials['username'], $credentials['password'],  $request->input('remember') !== NULL) 
            || Auth::attempt($credentials, $request->input('remember') !== NULL) 
        )
        {
            $user = Auth::user();

            if ($user->trial_time
                && $user->created_at->addHours($user->trial_time) <=  Carbon\Carbon::now()
            ){
                $user->role_id = 5; //Hopefully 5 mean guest.
            }

            if ($user->first_login_time == NULL) $user->first_login_time = now();
            else $user->last_login_time=now();
        
            $user->save();
            $path = parse_url(redirect()->intended(route('home'))->getTargetUrl())['path'];

            return redirect(url($path));
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
    public function authenticated(Request $request, $user) {


    }
}
