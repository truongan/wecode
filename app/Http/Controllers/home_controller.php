<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notification;
use App\Assignment;

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
    public function index()
    {
        // if (!in_array( Auth::user()->role->name, ['admin']) )
        // {
        //     $assignments = Auth::user()->lops()->with('assignments')->get()->pluck('assignments')->collapse()->keyBy('id')->sortByDesc('created_at');
        // }
        // else $assignments = Assignment::with('problems','lops')->latest()->get();
        // foreach ($assignments as &$assignment)
        // {
        //     $delay = strtotime(date("Y-m-d H:i:s")) - strtotime($assignment->finish_time);
        //     $submit_time = strtotime(date("Y-m-d H:i:s")) - strtotime($assignment->start_time);

        //     $assignment->coefficient = $assignment->eval_coefficient();// $coefficient;
        //     $assignment->finished = $assignment->is_finished();
        //     $assignment->no_of_problems = $assignment->problems->count();
        // }
        
        return view('home', ['selected' => 'dashboard', 'notifications'=>Notification::whereUser( Auth::user() )->paginate(3),'all_assignments'=> Assignment::with('lops')->where('id', '>', 0)->get()->filter(function($item){return $item->can_submit(Auth::user());}) ]);
    }
}
