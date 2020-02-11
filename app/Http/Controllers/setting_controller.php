<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class setting_controller extends Controller
{
    //
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $t = Setting::all();
        $data;
        foreach($t as $setting){
            $data[$setting->key] = $setting->value;
        }
        // $data['selected'] = 'settings';
        // var_dump($data);die();
        return view('admin.settings', $data);
    }
    
    public function update(Request $request){
        $t = Setting::all();
        
        $request->validate(['timezone' => 'required',
            ''
        ]);

        // var_dump($request->all());die();
        $keys=[
            ['key' => 'site_name'],
            ['key' => 'timezone'],
            ['key' => 'tester_path'],
            ['key' => 'assignments_root'],
            ['key' => 'file_size_limit'],
            ['key' => 'output_size_limit'],
            // ['key' => 'queue_is_working'],
            ['key' => 'default_late_rule'],
            ['key' => 'enable_c_shield'],
            // ['key' => 'enable_cpp_shield'],
            // ['key' => 'enable_py2_shield'],
            // ['key' => 'enable_py3_shield'],
            // ['key' => 'enable_java_policy'],
            // ['key' => 'enable_log'],
            ['key' => 'submit_penalty'],
            ['key' => 'enable_registration'],
            ['key' => 'registration_code'],
            ['key' => 'mail_from'],
            ['key' => 'mail_from_name'],
            ['key' => 'reset_password_mail'],
            ['key' => 'add_user_mail'],
            ['key' => 'moss_userid'],
            ['key' => 'results_per_page_all'],
            ['key' => 'results_per_page_final'],
            ['key' => 'week_start'],
            ['key' => 'theme'],
            ['key'=> 'concurent_queue_process'],
        ];

        //verify all the key is there
        foreach($keys as $k){
            var_dump ($k);var_dump($request->input($k));
        }
        die();
    }
}
