<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HtmlEditorControllerTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "html_editor_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	private function autosavePathFor(User $user): string
	{
		return sprintf("%s/%s/_htmleditor.auto.save.txt", Setting::get("assignments_root"), $user->id);
	}

	private function cleanupAutosaveFile(User $user): void
	{
		$path = $this->autosavePathFor($user);
		@unlink($path);
		@rmdir(dirname($path));
	}

	public function test_index_renders_default_content_for_admin(): void
	{
		$user = $this->makeUser(1);

		$response = $this->actingAs($user)->get("/htmleditor");

		$response->assertOk();
		$response->assertSee("INPUT", false);
		$response->assertSee("assets/tiptap/tiptap.min.js", false);
		$this->assertFileExists($this->autosavePathFor($user));

		$this->cleanupAutosaveFile($user);
	}

	public function test_index_renders_editor_feature_controls(): void
	{
		$user = $this->makeUser(1);

		$response = $this->actingAs($user)->get("/htmleditor");

		$response->assertOk();
		$response->assertSee("assets/tiptap/katex.min.css", false);
		$response->assertSee('data-cmd="inline_math"', false);
		$response->assertSee('data-cmd="source"', false);
		$response->assertSee('id="source_editor"', false);
		$this->assertFileExists(public_path("assets/tiptap/katex.min.css"));

		$this->cleanupAutosaveFile($user);
	}

	public function test_autosave_persists_content_and_returns_success(): void
	{
		$user = $this->makeUser(1);
		$this->actingAs($user)->get("/htmleditor");

		$response = $this->actingAs($user)->post("/htmleditor/autosave", ["content" => "<p>hello world</p>"]);

		$response->assertOk();
		$this->assertSame("success", $response->getContent());
		$this->assertSame("<p>hello world</p>", file_get_contents($this->autosavePathFor($user)));

		$this->cleanupAutosaveFile($user);
	}

	public function test_student_cannot_access_editor(): void
	{
		$user = $this->makeUser(4);

		$response = $this->actingAs($user)->get("/htmleditor");

		$response->assertNotFound();
	}

	public function test_guest_is_redirected_to_login(): void
	{
		$response = $this->get("/htmleditor");

		$response->assertRedirect(route("login"));
	}
}
