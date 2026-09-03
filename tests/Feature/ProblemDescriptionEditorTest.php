<?php

namespace Tests\Feature;

use App\Models\Assignment;
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
		foreach (glob($problem->get_directory_path() . "desc*.html") ?: [] as $description_file) {
			@unlink($description_file);
		}
		@rmdir($problem->get_directory_path());
	}

	private function makeAssignmentWith(Problem $problem, User $owner): Assignment
	{
		$assignment = Assignment::create([
			"name" => "problem_desc_test_" . uniqid(),
			"user_id" => $owner->id,
			"open" => 1,
			"score_board" => 1,
			"start_time" => now()->subDay(),
			"finish_time" => now()->addDay(),
			"extra_time" => 0,
		]);
		$assignment->problems()->attach($problem->id, ["problem_name" => $problem->name, "score" => 10, "ordering" => 1]);

		return $assignment;
	}

	private function writeDescription(Problem $problem, string $file_name, string $content): void
	{
		if (!is_dir($problem->get_directory_path())) {
			mkdir($problem->get_directory_path(), 0700, true);
		}
		file_put_contents($problem->get_directory_path() . $file_name, $content);
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

	public function test_admin_can_save_a_description_in_another_language(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");

		$response = $this->actingAs($user)->post(
			route("problems.edit_description", ["problem" => $problem->id, "language" => "en"]),
			["content" => "<p>statement</p>"],
		);

		$response->assertOk();
		$this->assertSame("<p>statement</p>", file_get_contents($problem->get_directory_path() . "desc.en.html"));
		$this->assertSame("<p>mô tả</p>", file_get_contents($problem->get_directory_path() . "desc.html"));
		$this->assertSame(["", "en"], $problem->available_languages());

		$this->cleanupProblemDirectory($problem);
	}

	public function test_saving_without_a_language_writes_the_unsuffixed_file(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		if (!is_dir($problem->get_directory_path())) {
			mkdir($problem->get_directory_path(), 0700, true);
		}

		$response = $this->actingAs($user)->post(route("problems.edit_description", $problem->id), [
			"content" => "<p>mô tả</p>",
		]);

		$response->assertOk();
		$this->assertSame("<p>mô tả</p>", file_get_contents($problem->get_directory_path() . "desc.html"));

		$this->cleanupProblemDirectory($problem);
	}

	public function test_saving_an_invalid_language_writes_the_unsuffixed_file(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		if (!is_dir($problem->get_directory_path())) {
			mkdir($problem->get_directory_path(), 0700, true);
		}

		$response = $this->actingAs($user)->post(
			route("problems.edit_description", ["problem" => $problem->id, "language" => "english"]),
			["content" => "<p>mô tả</p>"],
		);

		$response->assertOk();
		$this->assertSame(["" ], $problem->available_languages());
		$this->assertSame("<p>mô tả</p>", file_get_contents($problem->get_directory_path() . "desc.html"));

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
	public function test_description_defaults_to_the_unsuffixed_file(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$this->assertSame("<p>mô tả</p>", $problem->description()["description"]);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_description_reads_the_file_of_the_requested_language(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$this->assertSame("<p>statement</p>", $problem->description("en")["description"]);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_description_of_a_missing_language_is_not_found(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");

		$this->assertSame("<p>Description not found</p>", $problem->description("ja")["description"]);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_description_falls_back_to_the_default_file_for_an_invalid_language(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");

		$this->assertSame("<p>mô tả</p>", $problem->description("../../../etc/passwd")["description"]);
		$this->assertSame("<p>mô tả</p>", $problem->description(null)["description"]);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_practice_show_defaults_to_the_unsuffixed_description(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$response = $this->actingAs($student)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertSee("<p>mô tả</p>", false);
		$response->assertDontSee("<p>statement</p>", false);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_practice_show_serves_the_requested_language(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$response = $this->actingAs($student)->get(
			route("practices.show", ["problem" => $problem->id, "language" => "en"]),
		);

		$response->assertOk();
		$response->assertSee("<p>statement</p>", false);
		$response->assertDontSee("<p>mô tả</p>", false);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_practice_show_pdf_is_not_shadowed_by_the_language_segment(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));

		$response = $this->actingAs($student)->get(route("practices.show_pdf", $problem));

		// No pdf on disk, so show_pdf aborts with 404 instead of rendering the
		// problem page that practices.show would have returned.
		$response->assertNotFound();
	}

	public function test_practice_show_rejects_an_invalid_language_segment(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));

		$response = $this->actingAs($student)->get("/practice/show/" . $problem->id . "/english");

		$response->assertNotFound();
	}

	public function test_save_button_posts_to_the_default_language(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);

		$response = $this->actingAs($user)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertSee(route("problems.edit_description", $problem->id), false);
	}

	public function test_save_button_posts_to_the_language_being_viewed(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$response = $this->actingAs($user)->get(route("practices.show", ["problem" => $problem->id, "language" => "en"]));

		$response->assertOk();
		$response->assertSee(
			route("problems.edit_description", ["problem" => $problem->id, "language" => "en"]),
			false,
		);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_save_button_posts_to_the_language_being_viewed_in_an_assignment(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		$assignment = $this->makeAssignmentWith($problem, $user);
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$response = $this->actingAs($user)->get(
			route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id, "language" => "en"]),
		);

		$response->assertOk();
		$response->assertSee("<p>statement</p>", false);
		$response->assertSee(
			route("problems.edit_description", ["problem" => $problem->id, "language" => "en"]),
			false,
		);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_title_menu_links_every_available_language(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");
		$this->writeDescription($problem, "desc.ja.html", "<p>問題</p>");

		$response = $this->actingAs($student)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertSee(route("practices.show", $problem), false);
		$response->assertSee(route("practices.show", ["problem" => $problem->id, "language" => "en"]), false);
		$response->assertSee(route("practices.show", ["problem" => $problem->id, "language" => "ja"]), false);
		$response->assertSee("default", false);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_language_links_keep_the_assignment_route_parameters(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);
		$assignment = $this->makeAssignmentWith($problem, $user);
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$response = $this->actingAs($user)->get(
			route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id]),
		);

		$response->assertOk();
		$response->assertSee(
			route("assignments.show", [
				"assignment" => $assignment->id,
				"problem_id" => $problem->id,
				"language" => "en",
			]),
			false,
		);

		$this->cleanupProblemDirectory($problem);
	}

	public function test_editor_gets_the_add_language_button(): void
	{
		$user = $this->makeUser(1);
		$problem = $this->makeProblem($user);

		$response = $this->actingAs($user)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertSee("add-language-button", false);
		$response->assertSee("Add language", false);
		$response->assertSee(
			route("practices.show", ["problem" => $problem->id, "language" => "__language__"]),
			false,
		);
	}

	public function test_student_does_not_get_the_add_language_button(): void
	{
		$student = $this->makeUser(4);
		$problem = $this->makeProblem($this->makeUser(1));

		$response = $this->actingAs($student)->get(route("practices.show", $problem));

		$response->assertOk();
		$response->assertDontSee("add-language-button", false);
		$response->assertDontSee("Add language", false);
	}

	public function test_available_languages_lists_every_suffixed_description(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.vi.html", "<p>mô tả</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");
		$this->writeDescription($problem, "desc.pt-br.html", "<p>enunciado</p>");

		$this->assertSame(["en", "pt-br", "vi"], $problem->available_languages());

		$this->cleanupProblemDirectory($problem);
	}

	public function test_available_languages_reports_the_unsuffixed_file_as_an_empty_code(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");

		$this->assertSame([""], $problem->available_languages());

		$this->cleanupProblemDirectory($problem);
	}

	public function test_available_languages_sorts_the_unsuffixed_file_first(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");
		$this->writeDescription($problem, "desc.html", "<p>mô tả</p>");

		$this->assertSame(["", "en"], $problem->available_languages());

		$this->cleanupProblemDirectory($problem);
	}

	public function test_available_languages_ignores_unrelated_files(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));
		$this->writeDescription($problem, "desc.backup.old.html", "<p>backup</p>");
		$this->writeDescription($problem, "description.html", "<p>other</p>");
		$this->writeDescription($problem, "desc.en.html", "<p>statement</p>");

		$this->assertSame(["en"], $problem->available_languages());

		@unlink($problem->get_directory_path() . "description.html");
		$this->cleanupProblemDirectory($problem);
	}

	public function test_available_languages_is_empty_without_any_description(): void
	{
		$problem = $this->makeProblem($this->makeUser(1));

		$this->assertSame([], $problem->available_languages());
	}
}
