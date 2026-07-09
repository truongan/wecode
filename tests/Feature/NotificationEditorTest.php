<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationEditorTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "notif_editor_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	private function makeNotification(User $author): Notification
	{
		return Notification::create([
			"title" => "notif_editor_test_" . uniqid(),
			"text" => '<p>Hello <span data-type="inline-math" data-latex="e = mc^2"></span></p>',
			"description" => "",
			"author" => $author->id,
			"last_author" => $author->id,
		]);
	}

	public function test_create_form_uses_tiptap_instead_of_ckeditor(): void
	{
		$admin = $this->makeUser(1);

		$response = $this->actingAs($admin)->get(route("notifications.create"));

		$response->assertOk();
		$response->assertSee("assets/tiptap/tiptap.min.js", false);
		$response->assertSee("assets/js/tiptap_editor.js", false);
		$response->assertSee("assets/tiptap/katex.min.css", false);
		$response->assertSee("assets/styles/tiptap_editor.css", false);
		$response->assertSee("tiptap-toolbar", false);
		$response->assertSee('id="source_editor"', false);
		$response->assertSee('id="notif_text"', false);
		$response->assertDontSee("ckeditor", false);
	}

	public function test_edit_form_uses_tiptap_and_preloads_text(): void
	{
		$admin = $this->makeUser(1);
		$notification = $this->makeNotification($admin);

		$response = $this->actingAs($admin)->get(route("notifications.edit", $notification->id));

		$response->assertOk();
		$response->assertSee("assets/tiptap/tiptap.min.js", false);
		$response->assertSee("assets/js/tiptap_editor.js", false);
		$response->assertSee("tiptap-toolbar", false);
		$response->assertSee('data-latex="e = mc^2"', false);
		$response->assertDontSee("ckeditor", false);
	}

	public function test_show_renders_math_with_katex_and_no_mathjax(): void
	{
		$admin = $this->makeUser(1);
		$notification = $this->makeNotification($admin);
		$student = $this->makeUser(4);

		$response = $this->actingAs($student)->get(route("notifications.show", $notification->id));

		$response->assertOk();
		$response->assertSee("assets/tiptap/katex.min.js", false);
		$response->assertSee("assets/tiptap/auto-render.min.js", false);
		$response->assertSee('[data-type="inline-math"]', false);
		$response->assertDontSee("mathjax", false);
		$response->assertDontSee("ckeditor", false);
		$response->assertDontSee("assets/tiptap/tiptap.min.js", false);
	}

	public function test_list_renders_math_with_katex(): void
	{
		$admin = $this->makeUser(1);
		$notification = $this->makeNotification($admin);
		$student = $this->makeUser(4);

		$response = $this->actingAs($student)->get(route("notifications.index"));

		$response->assertOk();
		$response->assertSee("assets/tiptap/katex.min.css", false);
		$response->assertSee("assets/tiptap/katex.min.js", false);
		$response->assertSee("assets/tiptap/auto-render.min.js", false);
		$response->assertSee('querySelectorAll(".notif_text")', false);
		$response->assertSee('data-latex="e = mc^2"', false);
	}

	public function test_class_create_form_no_longer_loads_ckeditor(): void
	{
		$admin = $this->makeUser(1);

		$response = $this->actingAs($admin)->get(route("lops.create"));

		$response->assertOk();
		$response->assertDontSee("ckeditor", false);
	}

	public function test_ckeditor_and_mathjax_assets_are_removed(): void
	{
		$this->assertFileDoesNotExist(public_path("assets/ckeditor"));
		$this->assertFileDoesNotExist(public_path("assets/js/MathJax.2.7.9.js"));
		$this->assertFileExists(public_path("assets/tiptap/tiptap.min.js"));
	}
}
