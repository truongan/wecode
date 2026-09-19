<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Language;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AssignmentDescriptionLanguagesTest extends TestCase
{
	use DatabaseTransactions;

	private function makeAdmin(): User
	{
		return User::create([
			"username" => "desc_lang_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => 1,
		]);
	}

	private function makeProblem(User $owner): Problem
	{
		return Problem::create([
			"name" => "desc_lang_test_" . uniqid(),
			"diff_cmd" => "diff",
			"diff_arg" => "-bB",
			"allow_practice" => true,
			"user_id" => $owner->id,
		]);
	}

	private function makeAssignmentWith(Problem $problem, User $owner, ?string $allowed_problem_description_languages): Assignment
	{
		$assignment = Assignment::create([
			"name" => "desc_lang_test_" . uniqid(),
			"user_id" => $owner->id,
			"open" => 1,
			"score_board" => 1,
			"start_time" => now()->subDay(),
			"finish_time" => now()->addDay(),
			"extra_time" => 0,
			"allowed_problem_description_languages" => $allowed_problem_description_languages,
		]);
		$assignment->problems()->attach($problem->id, ["problem_name" => $problem->name, "score" => 10, "ordering" => 1]);

		return $assignment;
	}

	private function writeDescriptions(Problem $problem, array $file_names): void
	{
		if (!is_dir($problem->get_directory_path())) {
			mkdir($problem->get_directory_path(), 0700, true);
		}
		foreach ($file_names as $file_name) {
			file_put_contents($problem->get_directory_path() . $file_name, "content of $file_name");
		}
	}

	private function cleanup(Problem $problem): void
	{
		foreach (glob($problem->get_directory_path() . "desc*.html") ?: [] as $file) {
			@unlink($file);
		}
		@rmdir($problem->get_directory_path());
	}

	private function formInput(array $overrides = []): array
	{
		return array_merge(
			[
				"name" => "desc_lang_test_" . uniqid(),
				"extra_time" => "0*60*60",
				"start_time_date" => "2026-01-01",
				"start_time_time" => "00:00:00",
				"finish_time_date" => "2026-12-31",
				"finish_time_time" => "00:00:00",
				"language_ids" => [Language::first()->id],
				"problem_id" => [-1],
				"problem_name" => [""],
				"problem_score" => [0],
			],
			$overrides,
		);
	}

	public function test_store_normalises_the_description_languages(): void
	{
		$admin = $this->makeAdmin();

		$this->actingAs($admin)
			->post(route("assignments.store"), $this->formInput(["allowed_problem_description_languages" => " EN,vi , en, pt-BR "]))
			->assertSessionHasNoErrors();

		$assignment = Assignment::where("user_id", $admin->id)->latest("id")->first();
		$this->assertSame("en, vi, pt-br", $assignment->allowed_problem_description_languages);
		$this->assertSame(["en", "vi", "pt-br"], $assignment->allowed_description_languages());
	}

	public function test_store_saves_null_for_an_empty_list(): void
	{
		$admin = $this->makeAdmin();

		$this->actingAs($admin)
			->post(route("assignments.store"), $this->formInput(["allowed_problem_description_languages" => ""]))
			->assertSessionHasNoErrors();

		$assignment = Assignment::where("user_id", $admin->id)->latest("id")->first();
		$this->assertNull($assignment->allowed_problem_description_languages);
		$this->assertNull($assignment->allowed_description_languages());
	}

	public function test_store_rejects_invalid_language_codes(): void
	{
		$admin = $this->makeAdmin();

		$this->actingAs($admin)
			->post(route("assignments.store"), $this->formInput(["allowed_problem_description_languages" => "en, english"]))
			->assertSessionHasErrors("allowed_problem_description_languages");

		$this->assertSame(0, Assignment::where("user_id", $admin->id)->count());
	}

	public function test_update_changes_and_clears_the_description_languages(): void
	{
		$admin = $this->makeAdmin();
		$problem = $this->makeProblem($admin);
		$assignment = $this->makeAssignmentWith($problem, $admin, "en");

		$this->actingAs($admin)
			->put(route("assignments.update", $assignment), $this->formInput(["allowed_problem_description_languages" => "vi"]))
			->assertSessionHasNoErrors();
		$this->assertSame("vi", $assignment->fresh()->allowed_problem_description_languages);

		$this->actingAs($admin)
			->put(route("assignments.update", $assignment), $this->formInput(["allowed_problem_description_languages" => ""]))
			->assertSessionHasNoErrors();
		$this->assertNull($assignment->fresh()->allowed_problem_description_languages);
	}

	public function test_restricted_assignment_only_lists_allowed_languages(): void
	{
		$admin = $this->makeAdmin();
		$problem = $this->makeProblem($admin);
		$this->writeDescriptions($problem, ["desc.html", "desc.en.html", "desc.vi.html"]);
		$assignment = $this->makeAssignmentWith($problem, $admin, "vi");

		$response = $this->actingAs($admin)->get(
			route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id, "language" => "vi"]),
		);
		$response->assertOk();
		$response->assertSee("content of desc.vi.html", false);
		$response->assertDontSee(">default</a", false);
		$response->assertDontSee(">en</a", false);

		$this->cleanup($problem);
	}

	public function test_requested_language_is_shown_even_when_not_in_the_allowed_list(): void
	{
		$admin = $this->makeAdmin();
		$problem = $this->makeProblem($admin);
		$this->writeDescriptions($problem, ["desc.html", "desc.en.html", "desc.vi.html"]);
		$assignment = $this->makeAssignmentWith($problem, $admin, "vi");

		$response = $this->actingAs($admin)->get(
			route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id, "language" => "en"]),
		);

		$response->assertOk();
		$response->assertSee("content of desc.en.html", false);
		$response->assertDontSee(">en</a", false);

		$this->cleanup($problem);
	}

	public function test_unrestricted_assignment_lists_every_language(): void
	{
		$admin = $this->makeAdmin();
		$problem = $this->makeProblem($admin);
		$this->writeDescriptions($problem, ["desc.html", "desc.en.html", "desc.vi.html"]);
		$assignment = $this->makeAssignmentWith($problem, $admin, null);

		$response = $this->actingAs($admin)->get(
			route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id, "language" => "en"]),
		);
		$response->assertOk();
		$response->assertSee(">default</a", false);
		$response->assertSee(">vi</a", false);

		$this->cleanup($problem);
	}

	public function test_allowed_languages_are_listed_even_when_the_problem_has_no_such_description(): void
	{
		$admin = $this->makeAdmin();
		$problem = $this->makeProblem($admin);
		$this->writeDescriptions($problem, ["desc.html", "desc.en.html"]);
		$assignment = $this->makeAssignmentWith($problem, $admin, "vi");

		$response = $this->actingAs($admin)->get(
			route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id, "language" => "vi"]),
		);

		$response->assertOk();
		$response->assertSee(">vi</a", false);
		$response->assertSee("Description not found", false);
		$response->assertDontSee("content of desc.en.html", false);

		$this->cleanup($problem);
	}

	public function test_student_requesting_a_disallowed_language_is_redirected_to_the_first_allowed_one(): void
	{
		$admin = $this->makeAdmin();
		$problem = $this->makeProblem($admin);
		$assignment = $this->makeAssignmentWith($problem, $admin, "vi, en");
		$student = User::create([
			"username" => "desc_lang_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => 4,
		]);

		foreach ([null, "pt-br"] as $disallowed_language) {
			$this->actingAs($student)
				->get(route("assignments.show", array_filter([
					"assignment" => $assignment->id,
					"problem_id" => $problem->id,
					"language" => $disallowed_language,
				])))
				->assertRedirect(route("assignments.show", [
					"assignment" => $assignment->id,
					"problem_id" => $problem->id,
					"language" => "vi",
				]));
		}

		$this->actingAs($student)
			->get(route("assignments.show", ["assignment" => $assignment->id, "problem_id" => $problem->id, "language" => "en"]))
			->assertOk();
	}
}
