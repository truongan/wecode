<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Setting;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Submission;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Builder;
use ZipArchive;
use App\Http\Middleware\read_only_archive;

class problem_controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function __construct()
	{
		$this->middleware("auth"); // phải login
		$this->middleware(read_only_archive::class); // Make this controller read only when app is in archived mode.
	}

	private function _can_edit_or_404($problem)
	{
		if ($problem->can_edit(Auth::user())) {
			return true;
		} else {
			abort(404);
		}
	}

	public function index(Request $request)
	{
		if (in_array(Auth::user()->role->name, ["student"])) {
			// abort(403, 'No access');
		}
		if (Auth::user()->role->name == "admin") {
			$all_problem = Problem::latest();
		} else {
			$all_problem = Problem::available(Auth::user()->id)->latest();
		}

		if ($request->get("search") != "") {
			$all_problem->where(
				"name",
				"like",
				"%" . trim($request->get("search")) . "%",
			);
		}
		if ($request->get("tag_id") != null) {
			$all_problem->whereHas("tags", function (Builder $query) use (
				$request,
			) {
				$query->whereIn("tag_id", $request->get("tag_id"));
			});
		}

		$all_problem = $all_problem
			->with("assignments", "languages")
			->paginate(Setting::get("results_per_page_all"));
		$all_problem->appends(["search" => $request->get("search")]);

		$a = $all_problem->pluck("id");

		$total_subs = Submission::groupBy("problem_id")
			->whereIn("problem_id", $a)
			->select("problem_id", DB::raw("count(*) as total_sub"))
			->get()
			->keyBy("problem_id");
		$ac_subs = Submission::groupBy("problem_id")
			->whereIn("problem_id", $a)
			->where("pre_score", 10000)
			->select("problem_id", DB::raw("count(*) as total_sub"))
			->get()
			->keyBy("problem_id");

		foreach ($all_problem as $p) {
			$p->total_submit = $total_subs[$p->id]->total_sub ?? 0;
			$p->accepted_submit = $ac_subs[$p->id]->total_sub ?? 0;
			$p->ratio =
				round($p->accepted_submit / max($p->total_submit, 1), 2) * 100;
		}
		// dd(DB::getQueryLog());
		return view("problems.list", [
			"problems" => $all_problem,
			"all_tags" => Tag::all(),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		if (
			!in_array(Auth::user()->role->name, [
				"admin",
				"head_instructor",
				"instructor",
			])
		) {
			abort(404);
		}

		return view("problems.create", [
			"problem" => null,
			"all_languages" => Language::orderBy("sorting")->get(),
			"tree_dump" => "not found",
			"messages" => [],
			"languages" => [],
			"max_file_uploads" => ini_get("max_file_uploads"),
			"all_tags" => Tag::all(),
			"tags" => [],
		]);
	}
	private function add_missing_tags($tags)
	{
		foreach ($tags as $i => $tag) {
			if (Tag::find($tag) == null) {
				$tag = substr($tag, 1); // Remove the first character (which should be '#') from new tag

				if (Tag::where("text", $tag)->first() == []) {
					$new_tag = new Tag();
					$new_tag->text = $tag;
					$new_tag->save();
					$tags[$i] = (string) $new_tag->id;
				} else {
					array_splice($tags, $i, 1);
				}
			}
		}
		// dd($tags);
		return $tags;
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//dd($request->input('tag_id'));
		if (
			!in_array(Auth::user()->role->name, [
				"admin",
				"head_instructor",
				"instructor",
			])
		) {
			abort(404);
		}

		$validatedData = $request->validate([
			"name" => ["required", "max:255"],
		]);

		$tags = $request->input("tag_id");
		if ($tags != null) {
			$tags = $this->add_missing_tags($tags);
		}

		$langs = [];
		foreach ($request->input("enable") as $i => $lang) {
			if ($lang == 1) {
				$langs += [
					$request->input("language_id")[$i] => [
						"time_limit" => $request->input("time_limit")[$i],
						"memory_limit" => $request->input("memory_limit")[$i],
					],
				];
			}
		}

		//$default_language = Language::find(1);

		// $the_id = Problem::max('id') + 1;
		$problem = $request->input();
		// $problem['id'] = $the_id;
		$problem["user_id"] = Auth::user()->id;
		$problem["allow_practice"] = $request->has("allow_practice");
		$problem["sharable"] = $request->has("sharable");
		$problem["allow_input_download"] = $request->has(
			"allow_input_download",
		);
		$problem["allow_output_download"] = $request->has(
			"allow_output_download",
		);
		$p = Problem::create($problem);
		if ($tags != null) {
			$p->tags()->sync($tags);
		}

		$p->languages()->sync($langs);

		// Processing file
		$this->_take_test_file_upload($request, $p, $messages);

		return redirect()
			->route("problems.index")
			->withInput()
			->withErrors(["messages" => $messages]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Problem  $problem
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		abort(404, "Problem can be view through assignment or practice only");
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Problem  $problem
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Problem $problem)
	{
		$this->_can_edit_or_404($problem);
		$lang_of_problems = $problem->languages->keyBy("id");

		$tags = $problem->tags->keyBy("id");
		return view("problems.create", [
			"problem" => $problem,
			"all_languages" => Language::orderBy("sorting")->get(),
			"messages" => [],
			"languages" => $lang_of_problems,
			"tree_dump" => shell_exec(
				"tree -h " . $problem->get_directory_path(),
			),
			"max_file_uploads" => ini_get("max_file_uploads"),
			"all_tags" => Tag::all(),
			"tags" => $tags,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Problem  $problem
	 * @return \Illuminate\Http\Response
	 *
	 **/
	public function update(Request $request, Problem $problem)
	{
		$this->_can_edit_or_404($problem);

		$validatedData = $request->validate([
			"name" => ["required", "max:255"],
			"editorial" => "nullable|url",
		]);

		$req = $request->input();
		$req["allow_practice"] = $request->has("allow_practice");
		$req["sharable"] = $request->has("sharable");
		$problem["allow_input_download"] = $request->has(
			"allow_input_download",
		);
		$problem["allow_output_download"] = $request->has(
			"allow_output_download",
		);

		$problem->update($req);

		$tags = $request->input("tag_id");

		if ($tags != null) {
			$tags = $this->add_missing_tags($tags);
		}

		$problem->tags()->sync($tags);

		$this->_replace_problem($request, $problem);
		$this->_take_test_file_upload($request, $problem, $messages);

		return redirect()
			->route("problems.index")
			->withInput()
			->withErrors(["messages" => $messages]);
	}

	private function _take_test_file_upload(
		Request $request,
		Problem $problem,
		&$messages,
	) {
		$up_dir = $request->tests_dir;
		$up_zip = $request->tests_zip;
		if (!$up_dir && !$up_zip) {
			//             $messages = "Notice: You did not upload test case and description. If needed, upload by editing assignment.";
			return;
		}
		$assignments_root = Setting::get("assignments_root");
		$problem_dir = $problem->get_directory_path();

		if (!file_exists($problem_dir)) {
			mkdir($problem_dir, 0700, true);
		}

		if ($up_zip) {
			//Upload Tests (zip file)
			shell_exec("rm -f " . $assignments_root . "/*.zip");

			$name_zip = $request->tests_zip->getClientOriginalName();
			$path_zip = $request->tests_zip->storeAs(
				"",
				$name_zip,
				"assignment_root",
			);

			$this->unload_zip_test_file(
				$request,
				$assignments_root,
				$problem_dir,
				$messages,
				$name_zip,
			);
		} else {
			if ($up_dir) {
				$this->handle_test_dir_upload(
					$request,
					$assignments_root,
					$up_dir,
					$problem_dir,
					$messages,
				);
			}
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Problem  $problem
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id = null)
	{
		if (
			!in_array(Auth::user()->role->name, [
				"admin",
				"head_instructor",
				"instructor",
			])
		) {
			abort(404);
		} elseif ($id === null) {
			$json_result = ["done" => 0, "message" => "Input Error"];
		} else {
			$problem = Problem::find($id);

			$this->_can_edit_or_404($problem);

			$result["no_of_ass"] = $problem->assignments->count();
			$result["no_of_sub"] = $problem->submissions->count();
			$result["languages"] = $problem->languages;

			if ($problem == null) {
				$json_result = ["done" => 0, "message" => "Not found detailed"];
			} elseif (
				($problem["no_of_ass"] != 0) &
				($problem["no_of_sub"] != 0)
			) {
				$json_result = [
					"done" => 0,
					"message" =>
						"Problem already appear in assignments and got some submission should not be delete",
				];
			} else {
				$problem->delete();
				$json_result = ["done" => 1];
			}
		}

		header("Content-Type: application/json; charset=utf-8");
		return $json_result;
	}

	private function save_problem_description(
		Problem $problem,
		$text,
		$type = "html",
	) {
		$problem_dir = $problem->get_directory_path();
		if (file_put_contents("$problem_dir/desc.html", $text)) {
			return true;
		} else {
			return false;
		}
	}

	public function edit_description(Request $request, Problem $problem)
	{
		if (
			!in_array(Auth::user()->role->name, [
				"admin",
				"head_instructor",
				"instructor",
			])
		) {
			abort(404);
		}
		if ($this->save_problem_description($problem, $request->content)) {
			echo "success";
			return;
		} else {
			echo "error";
		}
	}

	private function unload_zip_test_file(
		Request $request,
		$assignments_root,
		$problem_dir,
		&$messages,
		$name_zip,
	) {
		$tmp_dir_name = "shj_tmp_directory";
		$tmp_dir = "$assignments_root/$tmp_dir_name";
		shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");

		// get new name
		$rename_inputoutput = $request->rename_zip;
		// extract file
		shell_exec(
			"cd $assignments_root; unzip " .
				escapeshellarg($name_zip) .
				" -d $tmp_dir",
		);

		// Remove the zip file
		shell_exec("cd $assignments_root; rm -rf " . escapeshellarg($name_zip));

		$this->clean_up_old_problem_dir($problem_dir);

		if (glob("$tmp_dir/*.pdf")) {
			shell_exec("cd $problem_dir; rm -f *.pdf");
		}

		shell_exec("cp -R $tmp_dir/* $problem_dir;");

		$in = glob("$problem_dir/in/*");
		$out = glob("$problem_dir/out/*");

		if ($in) {
			//rename input and output file base on file name order
			if ($rename_inputoutput) {
				// dd($rename_inputoutput);
				if (count($in) != count($out)) {
					$messages[] =
						"The zip contain mismatch number of input and output files: " .
						count($in) .
						" input files vs " .
						count($out) .
						" output files";
				} else {
					shell_exec("cd $problem_dir; rm -f in_old out_old");
					rename("$problem_dir/in", "$problem_dir/in_old");
					rename("$problem_dir/out", "$problem_dir/out_old");
					shell_exec("cd $problem_dir; mkdir in out");
					$in = glob("$problem_dir/in_old/*");
					$out = glob("$problem_dir/out_old/*");
					// dd($out);
					for ($i = 1; $i <= count($in); $i++) {
						// var_dump([$in[$i-1],"$problem_dir/in/input$i.txt"] );
						copy($in[$i - 1], "$problem_dir/in/input$i.txt");
						copy($out[$i - 1], "$problem_dir/out/output$i.txt");
					}
					shell_exec("cd $problem_dir; rm -f in_old out_old");
					shell_exec("rm -rf $problem_dir/in_old");
					shell_exec("rm -rf $problem_dir/out_old");
					// dd($in);
				}
			} else {
				//Check input and output file but won't rename
				// var_dump($problem_dir);die();$problem_dir."/out/output$i.txt"
				for ($i = 0; $i < count($in); $i++) {
					$real_id = $i + 1;
					if (
						!in_array(
							$problem_dir . "/in/input$real_id.txt",
							$in,
						)
					) {
						$messages[] = "A file name input$real_id.txt seem to be missing in your folder";
					} else {
						if (
							!in_array(
								$problem_dir . "/out/output$real_id.txt",
								$out,
							)
						) {
							$messages[] = "A file name output$real_id.txt seem to be missing in your folder";
						}
					}
				}
			}
		}


		// Remove temp directory
		shell_exec("rm -rf $tmp_dir");
	}

	private function clean_up_old_problem_dir($problem_dir)
	{
		$remove = " rm -rf $problem_dir/*";
		shell_exec($remove);

		mkdir("$problem_dir/in", 0700, true);
		mkdir("$problem_dir/out", 0700, true);
	}

	public function download_testcases(
		Problem $problem,
		Assignment $assignment,
		$type = null,
	) {
		$check = $assignment->can_submit(Auth::user(), $problem);
		if (!$check->can_submit) {
			// dd($problem);
			abort(403, $check->error_message);
		}

		if ($type != "in" && $type != "out") {
			abort(404, "What are you trying to download");
		}
		if ($type == "in" && $problem->allow_input_download == false) {
			abort(403, "This problem does not allow input donwload");
		}
		if ($type == "out" && $problem->allow_output_download == false) {
			abort(403, "This problem does not allow output donwload");
		}

		$assignments_root = Setting::get("assignments_root");
		$zipFile =
			$assignments_root .
			"/problem" .
			(string) $problem->id .
			"_" .
			$type .
			(string) date("Y-m-d_H-i") .
			".zip";
		$pathdir =
			$assignments_root .
			"/problems/" .
			(string) $problem->id .
			"/" .
			$type .
			"/";

		// dd("cd $pathdir && zip -r $zipFile *");
		$a = shell_exec("cd $pathdir && zip -r $zipFile *");
		// dd($a);
		return response()->download($zipFile)->deleteFileAfterSend();
	}

	public function export(Request $request)
	{
		$ids = explode(",", $request->input("ids"));
		$probs = Problem::whereIn("id", $ids)->get()->load("user");

		if (!in_array(Auth::user()->role->name, ["admin"])) {
			$probs = $probs->reject(fn(Problem $prob, int $key)
				=> !(
   					$prob->sharable && Auth::user()->role->name != "student"
				) && $prob->user->id != Auth::user()->id
			);
		}
		$probs->load("languages")->load("tags");

		$assignments_root = Setting::get("assignments_root");
		$zipFile =
			$assignments_root .
			"/problem-" .
			$probs->pluck("id")->implode("-") .
			"_tests_and_descriptions_" .
			(string) date("Y-m-d_H-i") .
			".zip";

		foreach ($probs as $prob) {
			$pathdir = $prob->get_directory_path();
			$metadata_file = $pathdir . "/problem.wecode.metadata.json";
			file_put_contents($metadata_file, $prob->toJSON(JSON_PRETTY_PRINT));
			$a = shell_exec(
				"cd $pathdir/.. && zip -r $zipFile  " .
					(string) $prob->id .
					"/*",
			);
			unlink($metadata_file);
		}

		if (file_exists($zipFile)) {
			return response()->download($zipFile)->deleteFileAfterSend();
		} else {
			abort(403, "You don't have permissions to any of those problems");
		}
	}

	public function import(Request $request)
	{
		$storage = Storage::disk("assignment_root");

		$request->validate([
			"zip_upload" => ["required"],
		]);
		if (!in_array(Auth::user()->role->name, ["admin", "head_instructor"])) {
			abort(403, "This feature is reserved for higher up only");
		}

		$zip_file_name = $request->zip_upload->store("", "assignment_root");

		$tmp_dir = uniqid("import_tmpdir_");
		$storage->makeDirectory($tmp_dir);

		try {
			$zip = new ZipArchive();
			$zip->open($storage->path($zip_file_name));
			$zip->extractTo($storage->path($tmp_dir));
		} catch (\Exection $ex) {
			$storage->delete($zip_file_name);
			$storage->deleteDirectory($tmp_dir);
			abort(403, $ex->getMessage());
		} finally {
			$storage->delete($zip_file_name);
		}

		$lang_to_id = Language::all()->pluck("id", "name");
		$error_message = [];
		foreach ($storage->directories($tmp_dir) as $prob_folder) {
			try {
				$metadata = json_decode(
					$storage->get("$prob_folder/problem.wecode.metadata.json"),
				);

				$problem = new Problem((array) $metadata);
				$problem->id = null;
				$problem->user_id = Auth::user()->id;
				$problem->admin_note .= sprintf(
					"\nIMPORTED: orignal user %s (%s), original updated at %s",
					$metadata->user->username,
					$metadata->user->email,
					$metadata->updated_at,
				);
				$problem->save();

				$langs = [];
				foreach ($metadata->languages as $lang) {
					if (!isset($lang_to_id[$lang->name])) {
						continue;
					}
					$langs[$lang_to_id[$lang->name]] = [
						"time_limit" => $lang->pivot->time_limit,
						"memory_limit" => $lang->pivot->memory_limit,
					];
				}
				$problem->languages()->sync($langs);

				$storage->makeDirectory("problems/{$problem->id}/");

				shell_exec(
					"cp -r " .
						escapeshellarg($storage->path($prob_folder)) .
						"/* " .
						$storage->path("problems/{$problem->id}/"),
				);
			} catch (\Exception $e) {
				$error_message[] =
					"Error importing problem " .
					basename($prob_folder) .
					" ==> " .
					$e->getMessage() .
					"\n";
			}
		}
		$storage->deleteDirectory($tmp_dir);
		// shell_exec("rm -rf $tmp_dir");
		return redirect()
			->route("problems.index")
			->withInput()
			->withErrors(["messages" => $error_message]);
	}

	private function handle_test_dir_upload(
		Request $request,
		$assignments_root,
		$up_dir,
		$problem_dir,
		&$messages,
	) {
		$tmp_dir_name = "shj_tmp_directory";
		$tmp_dir = "$assignments_root/$tmp_dir_name";
		shell_exec("rm -rf $tmp_dir; mkdir $tmp_dir;");

		foreach ($request->tests_dir as $item) {
			$item->storeAs(
				$tmp_dir_name,
				$item->getClientOriginalName(),
				"assignment_root",
			);
		}

		// path data
		$data = glob("$tmp_dir/*");

		//
		if (!in_array($tmp_dir . "/desc.html", $data)) {
			$messages[] =
				"Your test folder doesn't have desc.html file for problem description";
		}
		$in = $out = $files = [];

		for ($i = 0; $i < count($data); $i++) {
			// var_dump($data[$i]);
			$path = explode("string", $data[$i]);
			$name = explode("/", $data[$i]);
			$name_with_extension = explode(".", end($name));
			$prefix_name = $name_with_extension[0];

			if (substr($prefix_name, 0, 5) == "input") {
				$in[end($name)] = $data[$i];
			} elseif (substr($prefix_name, 0, 6) == "output") {
				$out[end($name)] = $data[$i];
			} else {
				$files[end($name)] = $data[$i];
			}
		}

		if (!isset($files["desc.html"])) {
			$messages[] =
				"Your test folder doesn't have desc.html file for problem description";
		}

		for ($i = 1; $i < count($in); $i++) {
			if (!isset($in["input$i.txt"])) {
				$messages[] = "A file name input$i.txt seem to be missing in your folder";
			} else {
				if (!isset($out["output$i.txt"])) {
					$messages[] = "A file name output$i.txt seem to be missing in your folder";
				}
			}
		}

		$this->clean_up_old_problem_dir($problem_dir);

		foreach ($in as $name => $tmp_name) {
			rename($tmp_name, "$problem_dir/in/$name");
		}

		foreach ($out as $name => $tmp_name) {
			rename($tmp_name, "$problem_dir/out/$name");
		}

		foreach ($files as $name => $tmp_name) {
			rename($tmp_name, "$problem_dir/$name");
		}
	}

	private function _replace_problem(Request $request, Problem $problem)
	{
		DB::beginTransaction();

		$time_limit = $request->time_limit;
		$memory_limit = $request->memory_limit;
		$enable = $request->enable;

		$problem->languages()->detach();

		for ($i = 0; $i < count($enable); $i++) {
			if ($enable[$i]) {
				$problem->languages()->attach($request->language_id[$i], [
					"time_limit" => $time_limit[$i],
					"memory_limit" => $memory_limit[$i],
				]);
			}
		}

		DB::commit();
	}

	public function toggle_practice(Request $request, string $query)
	{
		$a = explode(".", $query);
		// dd($a);
		$task = $a[0];
		$id = $a[1];

		$problem = Problem::find($id);
		$this->_can_edit_or_404($problem);

		if ($task == "practice") {
			$problem->allow_practice = !$problem->allow_practice;
			$problem->save();
			return $problem->allow_practice;
		} else {
			$problem->sharable = !$problem->sharable;
			$problem->save();
			return $problem->sharable;
		}
	}
	public function edit_tags(Request $request, Problem $problem)
	{
		$this->_can_edit_or_404($problem);
		$tags = $this->add_missing_tags($request->input("tag_id"));
		$problem->tags()->sync($tags);
		return json_encode([
			"all_tags" => Tag::all(),
			"new_tags" => $problem->tags,
		]);
	}
}
