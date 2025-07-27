<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

		$all_assignments = Assignment::all();
		$moss_userid = Setting::get('moss_userid');
		if ($moss_userid == null) 
			$moss_userid = -1;
		$moss_assignment = Assignment::assignment_info($assignment_id);
		$update_time = $assignment->moss_update;

		$moss_problems = array();

		foreach ($assignment->problems as $key => $problem){
			$moss_problems[$problem->id]['problem'] = $problem;
			$path = Submission::get_path('', $assignment_id, $problem->id) .'/' ;
			if (file_exists($path . "moss_link.txt") && file_get_contents($path . "moss_link.txt") != ''){
				$moss_problems[$problem->id]['moss'] = shell_exec("tail -n1 $path/moss_link.txt");
				shell_exec("rm $path/moss_running");
			} else if (file_exists($path . "moss_running")){
				$moss_problems[$problem->id]['moss'] = "submission submitted to moss, awaiting respone, please be patience";
			} else {
				$moss_problems[$problem->id]['moss'] = NULL;
			}
		}
		return view('admin.moss', ['all_assignments' => $all_assignments, 'moss_userid' => $moss_userid, 'moss_assignment' => $moss_assignment, 'update_time' => $update_time, 'moss_problems' => $moss_problems]); 
    }

    public function update(Request $request, $assignment_id = FALSE)
	{
		if ($assignment_id === FALSE)
			abort(404);
		$userid = $request->moss_userid;
		Setting::set('moss_userid', $userid);
		$moss_original = trim( file_get_contents(rtrim(Setting::get('tester_path'), '/').'/moss_original') );
		$moss_path = rtrim(Setting::get('tester_path'), '/').'/moss';
		file_put_contents($moss_path, str_replace('MOSS_USER_ID', $userid, $moss_original));
		echo(shell_exec("chmod +x {$moss_path}"));
		
		return redirect()->route('moss.index', ['id' => $assignment_id]);
	}


	public function detect(Request $request, $assignment_id = FALSE)
	{
		$validated = $request->validate([
            'detect' => 'required|required_with_all:detect',
        ]);
        $this->_detect($assignment_id);
        return redirect()->route('moss.index', ['id' => $assignment_id]);
	}

	private function _detect($assignment_id = FALSE)
	{
		if ($assignment_id === FALSE)
			abort(404);

		$lang = Language::all();

		$assignments_path = rtrim(Setting::get('assignments_root'), '/');
		$tester_path = rtrim(Setting::get('tester_path'), '/');
		shell_exec("chmod +x {$tester_path}/moss");
		$items = Submission::get_final_submissions($assignment_id);
		$groups = array();
		foreach ($items as $item) {
			if (!isset($groups[$item->problem_id]))
				$groups[$item->problem_id] = array($item);
			else
				array_push($groups[$item->problem_id], $item);
		}
		foreach ($groups as $problem_id => $group) {
			$list = '';
			$assignment_path = $assignments_path."/assignment_{$assignment_id}";
			foreach ($group as $item){
				$list .= "problem_{$problem_id}/{$item->username}/{$item->file_name}." .(string)Language::find($item->language_id)->extension . " ";
			}
			// echo "list='$list'; cd $assignment_path; $tester_path/moss \$list > problem_{$problem_id}/moss_link.txt  2>&1 &"; 			die();

			shell_exec("list='$list'; cd $assignment_path; $tester_path/moss \$list > problem_{$problem_id}/moss_link.txt  2>&1 &");
			shell_exec("cd $assignment_path/problem_{$problem_id}; touch moss_running");

		}
		
		Assignment::where('id', $assignment_id)->update(['moss_update' => date('Y-m-d H:i:s')]);
	}
}
