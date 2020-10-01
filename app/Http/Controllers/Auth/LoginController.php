<?php

namespace App\Http\Controllers\Auth;

use App\Setting;

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

    public function Login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if ( Auth::viaRemember() || 
            Auth::attempt($credentials, $request->input('remember') !== NULL)
        ) {

            if (Auth::user()->first_login_time == NULL) Auth::user()->first_login_time = now();
            else Auth::user()->last_login_time=now();
            
            Auth::user()->save();
            
            return redirect()->route('home');
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
