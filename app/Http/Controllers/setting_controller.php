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
        
        if ($request->validate(['timezone' => 'required',
            ''
        ])){
            $submitted = $request->input();
            // var_dump($request->all());die();
            $keys=[
                 'site_name',
                 'timezone',
                //  'tester_path',
                //  'assignments_root',
                 'file_size_limit',
                 'output_size_limit',
                //  'queue_is_working',
                 'default_late_rule',
                //  'enable_c_shield',
                //  'enable_cpp_shield',
                //  'enable_py2_shield',
                //  'enable_py3_shield',
                //  'enable_java_policy',
                //  'enable_log',
                 'submit_penalty',
                 'enable_registration',
                 'registration_code',
                 'mail_from',
                 'mail_from_name',
                 'reset_password_mail',
                 'add_user_mail',
                //  'moss_userid',
                 'results_per_page_all',
                 'results_per_page_final',
                 'week_start',
                 'theme',
                'concurent_queue_process',
                'default_language_number',
            ];
            // var_dump($submitted);die();
            if (!isset($submitted['enable_registration']))  $submitted['enable_registration'] = 0;
            foreach($keys as $k){
                // var_dump ($k);var_dump($submitted[$k]);
                Setting::updateOrCreate(['key' => $k], ['value' => $submitted[$k]]);
            }
            return back();
        }
    }
}
