<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\Role;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        // if (Setting::get('enable_registration') == false) abort(403, 'admin has disable new user registration');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (Setting::get('enable_registration') == false) abort(403, 'admin has disable new user registration');
        $a = 'regex:/'.  Setting::get('registration_code') . "/";

        return Validator::make($data, [
            'registration_code' => [$a],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if (Setting::get('enable_registration') == false) abort(403, 'admin has disable new user registration');

        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'display_name' => $data['display_name'],
            'password' => Hash::make($data['password']),
            'role_id' => Role::where('name','student')->first()->id,
            'trial_time' => Setting::get('default_trial_time')
        ]);
    }
}
