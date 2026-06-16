<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class html_editor_controller extends Controller
{
	private string $autosavePath;

	public function __construct()
	{
		$this->middleware("auth");
		$this->middleware(function ($request, $next) {
			// We need this so that the 'auth' middleware will run before the following line of code
			$this->autosavePath = sprintf("%s/%s/_htmleditor.auto.save.txt", Setting::get("assignments_root"), Auth::id());

			return $next($request);
		});
	}

	public function index(): View
	{
		if (!in_array(Auth::user()->role->name, ["admin", "head_instructor", "instructor"])) {
			abort(404);
		}

		$dir = pathinfo($this->autosavePath, PATHINFO_DIRNAME);
		if (!is_dir($dir)) {
			mkdir($dir, 0700, true);
		}

		$content = file_exists($this->autosavePath) ? file_get_contents($this->autosavePath) : "";

		if ($content === "") {
			$content = view("html_editor.default_content")->render();
		}

		file_put_contents($this->autosavePath, $content);

		return view("html_edit", ["selected" => "instructor_panel", "content" => $content]);
	}

	public function autosave(Request $request): Response
	{
		$saved = file_put_contents($this->autosavePath, $request->input("content", ""));

		if ($saved === false) {
			return response("error", 500);
		}

		return response("success");
	}
}
