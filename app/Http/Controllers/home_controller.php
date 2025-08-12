<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Assignment;

class home_controller extends Controller
{
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
	public function index(Request $request)
	{
		// dd($request->ips());
		return view('home', ['selected' => 'dashboard', 'notifications'=>Notification::latest()->paginate(3),'all_assignments'=> Assignment::with('lops')->where('id', '>', 0)->get()->filter(function($item){return $item->can_submit(Auth::user());}) ]);
	}
}
