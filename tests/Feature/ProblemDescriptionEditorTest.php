<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProblemDescriptionEditorTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "problem_desc_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	private function makeProblem(User $owner): Problem
	{
		return Problem::create([
			"name" => "problem_desc_test_" . uniqid(),
			"diff_cmd" => "diff",
			"diff_arg" => "-bB",
			"allow_practice" => true,
			"user_id" => $owner->id,
		]);
	}

	private function cleanupProblemDirectory(Problem $problem): void
	{
		@unlink($problem->get_directory_path() . "desc.html");
		@rmdir($problem->get_directory_path());
	}

	public function test_admin_sees_tiptap_editor_instead_of_ckeditor(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);

		$response = $this->actingAs($user)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertSee("assets/tiptap/tiptap.min.js", false);
		$response->assertSee("assets/js/tiptap_editor.js", false);
		$response->assertSee("assets/tiptap/katex.min.css", false);
		$response->assertSee("assets/styles/tiptap_editor.css", false);
		$response->assertSee("tiptap-toolbar", false);
		$response->assertSee('data-cmd="inline_math"', false);
		$response->assertSee('id="source_editor"', false);
		$response->assertSee(route("problems.edit_description", $problem->id), false);
		$response->assertDontSee("ckeditor.js", false);
		$response->assertDontSee("contenteditable", false);
	}

	public function test_student_sees_katex_view_without_editor(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));

		$response = $this->actingAs($student)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertSee("assets/tiptap/katex.min.js", false);
		$response->assertSee("assets/tiptap/auto-render.min.js", false);
		$response->assertDontSee("mathjax", false);
		$this->assertFileExists(public_path("assets/tiptap/katex.min.js"));
		$this->assertFileExists(public_path("assets/tiptap/auto-render.min.js"));
		$response->assertSee('[data-type="inline-math"]', false);
		$response->assertDontSee("assets/tiptap/tiptap.min.js", false);
		$response->assertDontSee("tiptap-toolbar", false);
		$response->assertDontSee('id="source_editor"', false);
	}

	public function test_admin_can_save_description(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		if (!is_dir($problem->get_directory_path())) {
			mkdir($problem->get_directory_path(), 0700, true);
		}

		$response = $this->actingAs($user)->post(route("problems.edit_description", $problem->id), [
			"content" => '<p>updated <span data-type="inline-math" data-latex="e = mc^2"></span></p>',
		]);

		$response->assertOk();
		$this->assertSame("success", $response->getContent());
		$this->assertSame(
			'<p>updated <span data-type="inline-math" data-latex="e = mc^2"></span></p>',
			file_get_contents($problem->get_directory_path() . "desc.html"),
		);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_student_cannot_save_description(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));

		$response = $this->actingAs($student)->post(route("problems.edit_description", $problem->id), [
			"content" => "<p>hacked</p>",
		]);

		$response->assertNotFound();
	}
}
