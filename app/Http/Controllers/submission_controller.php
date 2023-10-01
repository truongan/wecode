<?php

namespace App\Http\Controllers;

use App\Submission;
use App\Assignment;
use App\Problem;
use App\Queue_item;
use App\Language;
use App\Scoreboard;
use App\Setting;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\View\Components\submission\verdict;

class submission_controller extends Controller
{
	public function __construct()
	{
		$this->middleware('auth'); // phải login


	}

	private function _do_access_check($submission){
		if (in_array(Auth::user()->role->name, ['student', 'guest'])){
			if ($submission->user_id!=Auth::user()->id)
				abort(403,"You don't have permission to view another user's submissions");
			if (!$submission->assignment->open)
				abort(403,"This assignment has been close for students");
		}  
	}

	//abort on invalid creation

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($assignment_id = NULL, $user_id = 'all', $problem_id = 'all', $choose = 'all')
	{
		// if ($assignment_id == 0)
		//     abort(403,'You have not selected assignment');
		
		if (Assignment::find($assignment_id) == null)
		{
			return redirect()->route('submissions.index', [0, 'all', 'all', 'all']);
		}

		$assignment = Assignment::with('lops.users')//This is to display lop info for each submissions
						->find($assignment_id);

        if (Auth::user()->role->name == 'admin'){
            // Admin can view anything
		} 
		else if (  in_array( Auth::user()->role->name, ['head_instructor', 'instructor']) 
					&& $assignment->id != 0 //Allow instructors to view any practice submissions
		){
            if ($assignment->user != Auth::user() 
                && !Auth::user()->lops()->with('assignments')->get()->pluck('assignments')->collapse()->pluck('id')->contains($assignment->id)
            ){
                abort(403, 'You can only view submissions for assignment you created or assignment belongs to one of your classes');
            }
        }

		Auth::user()->selected_assignment_id = $assignment_id;
		Auth::user()->save(); 

		$submissions =$assignment->submissions();;
		if ( in_array( Auth::user()->role->name, ['student', 'guest']) )
		{
			//Student can only view their own submissions, regardless of assignment, so we don't check assignment permissions for student
			$submissions = $submissions->where('user_id',Auth::user()->id);
		}
		else if ($user_id != 'all'){
			$submissions = $submissions->where('user_id',intval($user_id));
		}
		

		if ($choose == 'final'){
			$submissions = $submissions->where('is_final',1);
		}
		if ($problem_id != 'all'){
			$submissions = $submissions->where('problem_id',intval($problem_id));
		}
		
		$submissions = $submissions->with(['language','user'])->latest()->paginate(Setting::get('results_per_page_all'));
		$all_problems = Assignment::find($assignment_id)->problems->keyBy('id');
		foreach ($submissions as &$submission){
			$submission->delay = $assignment->finish_time->diffAsCarbonInterval($submission->created_at, false);
			$this->_status($submission, $all_problems);
		}


		return view('submissions.list',['submissions' => $submissions, 'assignment' => $assignment, 'user_id' => $user_id, 'problem_id' => $problem_id, 'choose' => $choose, 'all_problems' => $all_problems]); 
	}

	private function _creation_guard_check($assignment_id, $problem_id, $language_id = -1){
		$assignment = Assignment::find($assignment_id);
		
		if ($assignment_id == 0){
			$problem = Problem::find($problem_id);
			if ($problem->can_practice(Auth::user()) == false ){
				abort(404);
			}
		}
		else {
			$problem = $assignment->problems->find($problem_id);
			if ($problem == NULL) abort(404);

			$check = $assignment->can_submit(Auth::user());
			if (!$check->can_submit){
				abort(403, $check->error_message);
			}
			
			if ($language_id != -1 && !in_array($language_id, explode(", ", $assignment->language_ids))){
				abort(403, " This assignment doesn't allow programming language of id " . $language_id) ;
			}
		} 

		return $problem;

	}

	public function create($assignment_id, $problem_id, $old_sub = -1){
		$assignment = Assignment::find($assignment_id);
		
		$problem = $this->_creation_guard_check($assignment_id, $problem_id);

		$last = Submission::where(['assignment_id' => $assignment_id, 'problem_id' => $problem_id, 'user_id' => Auth::user()->id]);
		if ($old_sub != -1) $last = $last->where(['id'=> $old_sub]);

		$last = $last->get()->last();
		

		$last_code = null;
		if ($last != null){
			$submit_path = Submission::get_path($last->user->username, $last->assignment_id, $last->problem_id);
			$file_extension = $last->language->extension;
	
			$file_path = $submit_path . "/{$last->file_name}.". $file_extension;
			$last_code = file_exists($file_path)? file_get_contents($file_path): null;
		}

		return view('submissions.create', ['assignment' => $assignment, 'problem' => $problem, 'last_code' => $last_code]);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'assignment' => ['integer', 'gt:-1'],
			'problem' => ['integer', 'gt:0'],
		]);
		// dd($request->input('language'));
		$this->_creation_guard_check($request->input('assignment'), $request->input('problem'), $request->input('language'));
		if ($this->upload($request))
			return redirect()->route('submissions.index', [$request->assignment, 'all', 'all', 'all']);
		else
			abort(501,'Error Uploading File');
	}
	

 
	public function upload_file_code($request, $user_dir, $submission)
	{
		if ($request->userfile->getSize() > Setting::get("file_size_limit") * 1024 )
			abort(403, "Your submission is larger than system limited size");

		// We use assignments submit count in the name beause newly created submission has not been assigned an id yet.
		$file_name = "solution-upload-count".($submission->assignment->total_submits);
		
		$path = Storage::disk('assignment_root')->path('');
		$user_dir = substr($user_dir, strlen($path));

		$path = $request->userfile->storeAs($user_dir, $file_name.".".$submission->language->extension, 'assignment_root');
		// dd($path);
		if ($path)
		{      
			$this->add_to_queue($submission, $submission->assignment, $file_name);   
			return TRUE;
		}
		
		return FALSE;
	}

	public function upload_post_code($code, $user_dir, $submission)
	{
		if (strlen($code) > Setting::get("file_size_limit") * 1024 )
			//string length larger tan file size limit
			abort(403, "Your submission is larger than system limited size");

		$ext = $submission->language->extension;
		$file_name = "solution-editcode-count" .($submission->assignment->total_submits);
		file_put_contents("{$user_dir}/${file_name}"
							. "." . $ext, $code);

		
		$this->add_to_queue($submission, $submission->assignment
								, "{$file_name}");
		return TRUE;
	}

	private function _in_queue ($user_id, $assignment_id, $problem_id)
	{
		return Queue_item::whereHas('submission', function($q) use($user_id, $assignment_id, $problem_id){$q->where(['user_id' => $user_id, 'assignment_id' => $assignment_id, 'problem_id' => $problem_id]);})->count() > 0;

	}

	private function add_to_queue($submission, $assignment, $file_name)
	{
		$assignment->increment('total_submits');
		$submission->file_name = $file_name;
		$submission->save();

		Queue_item::add_and_process($submission->id, 'judge');
		
	}

	private function get_path($username, $assignment_id, $problem_id)
	{
		$assignment_root = rtrim(Setting::get("assignments_root"),'/');
		return $assignment_root . "/assignment_{$assignment_id}/problem_{$problem_id}/{$username}";
	}


	public function rejudge_view(Assignment $assignment)
	{
		if (!in_array( Auth::user()->role->name, ['admin', 'head_instructor']))
			abort(403,"You don't have permission to do that");
		if (!$assignment->is_participant(Auth::user())) abort(403, "You are not a participant to that assignment");

		return view('submissions.rejudge', ['assignment' => $assignment, 'problems' => $assignment->problems]);
	}


	public function rejudge_all_problems_assignment(Request $request)
	{
		if (!in_array( Auth::user()->role->name, ['admin', 'head_instructor', 'instructor']))
			abort(403,"You don't have permission to do that");
		if ($request->assignment_id != null)
			$assignment = Assignment::with('problems')->find($request->assignment_id);
		else {
			abort(404, 'Need assignment to do rejudge all');
		}
			

		if ($request->problem_id == 'all')
			$submissions = Submission::where('assignment_id',$assignment->id)->get();
		else
			$submissions = Submission::where('assignment_id',$assignment->id)->where('problem_id', $request->problem_id)->get();
		
		if ($submissions->count() == 0){
			abort(404, 'invalid assignment_id and problem_id combo');
		}
		foreach ($submissions as $submission)
		{
			$a = Queue_item::add_not_process($submission->id, 'rejudge');
			// $submission->is_final = 0;
			$submission->status = 'PENDING';
			$submission->save();
		}
		for ($i=0; $i < Setting::get('concurent_queue_process', 2); $i++) { 
			Queue_item::work();
		}
		return redirect()->back()->with('status', 'Rejudge in progress');   
	}

	public function rejudge(Request $request){
		if (!in_array( Auth::user()->role->name, ['student', 'guest'])){
			$validated = $request->validate([
				'submission_id' => ['integer'],
			]);	
			
			$sub = Submission::find($request->submission_id);
			
			if ($sub == NULL) abort(404);
			
			if (Queue_item::where('submission_id', $sub->id)->count() > 0){
				return response()->json(['done' => 0, 'message' => 'Submission is already in queue for judging']);
			}

			$a = Queue_item::add_and_process($sub->id, 'rejudge');
			$sub->status = 'PENDING';
			$sub->save();
			
			return response()->json(
				['done' => 1]
			);
		}
	}

	public function get_template(Request $request){
		$validated = $request->validate([
			'assignment_id' => ['integer'],
			'problem_id' => 'integer',
			'language_id' => 'integer'
		]);
		
		$problem = $this->_creation_guard_check($request->input('assignment_id'), $request->input('problem_id'));

		$template = $problem->template_content($request->input('language_id'));
		
		if ($template == NULL)
			$result = array('banned' => '', 'before'  => '', 'after' => '');

		preg_match("/(\/\*###Begin banned.*\n)((.*\n)*)(###End banned keyword\*\/)/"
			, $template, $matches
		);
	
		$set_or_empty = function($arr, $key){
			if(isset($arr[$key])) return $arr[$key];
			return "";
		};

		$banned = $set_or_empty($matches, 2);

		preg_match("/(###End banned keyword\*\/\n)((.*\n)*)\/\/###INSERT CODE HERE -\n?((.*\n?)*)/"
			, $template, $matches
		);

		$before = $set_or_empty($matches, 2);
		$after = $set_or_empty($matches, 4);

		$result = array('banned' => $banned, 'before'  => $before, 'after' => $after);

		return response()->json($result);

	}

	public function upload($request)
	{
		$problem = Problem::find($request->problem);
		$assignment = Assignment::find($request->assignment);
		$language = Language::find($request->language);

		$coefficient = 100;
		if ($assignment->id == 0) {
			//Practice 
			if (!in_array( Auth::user()->role->name, ['admin', 'head_instructor']) && $problem->allow_practice!=1)
				abort(403,'This problem is not open for practice');
		}
		else
		{

			$coefficient = $assignment->eval_coefficient();


			$a = $assignment->can_submit(Auth::user());
			if(!$a->can_submit) abort(403, $a->error_message);

			if ($this->_in_queue(Auth::user()->id, $assignment->id, $problem->id))
				abort(403,'You have already submitted for this problem. Your last submission is still in queue.');

			if ($problem->languages->where('id',$language->id)->count() == 0)
				abort(403,'This file type is not allowed for this problem.');
		}

		$submission = new Submission ([
			'assignment_id' => $assignment->id,
			'problem_id' => $problem->id,
			'user_id' => Auth::user()->id,
			'is_final' => 0,
			'status' => 'pending',
			'pre_score' => 0,
			'judgement' => "",
			'coefficient' => $coefficient,
			'file_name' => null,
			'language_id' => $language->id,
		]);

		$user_dir = Submission::get_path(Auth::user()->username, $assignment->id, $problem->id);

		if (!file_exists($user_dir)){
			mkdir($user_dir, 0700, TRUE);
		}

		$code = $request->code;
		if ($code != NULL)
			return $this->upload_post_code($code, $user_dir, $submission);
		else 
		{
			if ($request->hasFile('userfile')) 
			{
				return $this->upload_file_code($request, $user_dir, $submission);
			}
			else abort(403,'No file chosen');
		}
	}

	public function select_final(Request $request)
	{
		$submission_curr = Submission::find($request->submission);
		if (!$submission_curr) abort(403,"Submission not found");
		$this->_do_access_check($submission_curr);

		$submission_final = Submission::where(array('user_id' => $submission_curr->user_id, 'assignment_id' => $submission_curr->assignment_id, 'problem_id' => $submission_curr->problem_id, 'is_final' => 1))->update(['is_final' => 0]);

		$submission_curr->is_final = 1;
		$submission_curr->save();

		Scoreboard::update_scoreboard( $submission_curr->assignment_id	);
		return response()->json(
			['done' => 1]
		);
	}
	public function view_code()
	{
		$submit_id = $_POST['submit_id'];
		$type = $_POST['type'];

		$submission = Submission::with('assignment')->find($submit_id);

		if (!$submission) abort(403,"Submission not found");
		$this->_do_access_check($submission);

		$submit_path = Submission::get_path($submission->user->username, $submission->assignment_id, $submission->problem_id);
		$file_extension = $submission->language->extension;

		if ($type == "code")
			$file_path = $submit_path . "/{$submission->file_name}.". $file_extension;
		elseif ($type == "log")
			$file_path = $submit_path . "/log-{$submission->id}";
		elseif ($type == "result")
			$file_path = $submit_path . "/result-{$submission->id}.html";


		$file_content = file_exists($file_path) ? file_get_contents($file_path) : "File not found";
		$file_content = (mb_convert_encoding($file_content,'UTF-8', 'ISO-8859-1'));

		$result = array(
				'file_name' => $submission->file_name .'.'. $file_extension,
				'text' => $file_content
				// 'text' => Storage::disk('my_local')->exists($file_path) ? Storage::disk('my_local')->get($file_path):"File Not Found"
			);
		if ($type === 'code') {
				$result['lang'] = $file_extension;
				if ($result['lang'] == 'py2' || $result['lang'] == 'py3')
					$result['lang'] = 'python';
				else if ($result['lang'] == 'pas')
					$result['lang'] = 'pascal';
			}
			
		return $result;
	}


	private function _status(&$submission, $all_problems = null)
	{
		if ($all_problems == null) $all_problems = $submission->assignment->problems->keyBy('id');
//If we can't find the assignment's score for problem (in case of practice), default to 100
		$score = ceil($submission->pre_score*
							($all_problems[$submission->problem_id]->pivot->score??100)
							/10000);
		if ($submission->coefficient == 'error')
			$submission->final_score = $score;
		else
			$submission->final_score = ceil($score*$submission->coefficient/100);
	}

	public function view_status(){
		
		$submit_id = $_POST['submit_id'];

		$submission = Submission::with('assignment')->find($submit_id);

		if (!$submission) abort(403,"Submission not found");
		$this->_do_access_check($submission);

		$this->_status($submission);

		$a = new verdict($submission);

		$submission->rendered_verdict = $a->resolveView()->with($a->data())->render();
		echo json_encode($submission);
		
	}
}
