<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Setting;
use App\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class moss_controller extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($assignment_id)
    {
    	if ( !in_array( Auth::user()->role->name, ['admin', 'head_instructor']) )
            abort(403);
        $assignment = Assignment::find($assignment_id);
        if ($assignment == null)
        	abort(404, 'Can not find this assignment!');

        $data = array(
			'all_assignments' => Assignment::all(),
			'moss_userid' => Setting::get('moss_userid'),
			'moss_assignment' => $assignment_id,
			'update_time' => $assignment->moss_update,
		);

		$data['moss_problems'] = array();

		foreach ($assignment->problems as $pid => $problemas){
			$data['moss_problems'][$pid] = NULL;
			
			$path = Submission::get_path('', $assignment_id, $pid) .'/' ;
			if (file_exists($path . "moss_link.txt") && file_get_contents($path . "moss_link.txt") != ''){
				$data['moss_problems'][$pid] = shell_exec("tail -n1 $path/moss_link.txt");
				shell_exec("rm $path/moss_running");
			} else if (file_exists($path . "moss_running")){
				$data['moss_problems'][$pid] = "submission submitted to moss, awaiting respone, please be patience";
			}
		}

		return view('admin.moss', $data); 
    }
}
