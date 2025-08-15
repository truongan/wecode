<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Rules\IPranges;
use Illuminate\Support\Facades\Auth;

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
		if (in_array(Auth::user()->role->name, ["student"])) {
			abort(403, 'No access');
		}
		$t = Setting::all();

		$data = array();
		foreach($t as $setting){
			$data[$setting->key] = $setting->value;
		}
		return view('admin.settings', $data);
	}

	public function update(Request $request){
		$t = Setting::all();
		if (!in_array(Auth::user()->role->name, ["admin"])) {
			abort(403, 'No access');
		}
		if ($request->validate([
				'timezone' => 'required',
				'ip_white_list' => [new IPranges]
			])
		)
		{
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
				 'submit_penalty',
				 'enable_registration',
				 'registration_code',
				//  'mail_from',
				//  'mail_from_name',
				//  'reset_password_mail',
				//  'add_user_mail',
				//  'moss_userid',
				 'results_per_page_all',
				 'results_per_page_final',
				 'week_start',
				 'theme',
				'concurent_queue_process',
				'default_language_number',
				'default_trial_time',
				'ip_white_list',
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
