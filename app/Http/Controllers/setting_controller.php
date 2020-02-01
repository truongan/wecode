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
        // var_dump($data);die();
        return view('settings', $data);
    }
    
    public function update(Request $request){

    }
}
