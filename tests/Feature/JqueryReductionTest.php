<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JqueryReductionTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "jquery_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	/**
	 * Every Blade view plus the hand written scripts, keyed by path. The
	 * vendored `*.min.js` bundles are skipped; only our own code is checked.
	 *
	 * @return array<string, string>
	 */
	private function projectScriptSources(): array
	{
		$paths = glob(resource_path("views") . "/{,*/,*/*/}*.blade.php", GLOB_BRACE) ?: [];
		$paths = array_merge($paths, array_filter(glob(public_path("assets/js") . "/*.js") ?: [], fn ($path) => !str_ends_with($path, ".min.js")));

		$sources = [];
		foreach ($paths as $path) {
			$sources[$path] = file_get_contents($path);
		}

		return $sources;
	}

	public function test_the_bare_layout_does_not_load_jquery(): void
	{
		$layout = file_get_contents(resource_path("views/layouts/bare.blade.php"));

		$this->assertStringNotContainsString("jquery", $layout);
		$this->assertStringContainsString("assets/js/bootstrap.bundle.min.js", $layout);
	}

	/**
	 * The views rendered through the bare layout carry no JavaScript of their
	 * own, so the auth pages must come back without jQuery on them.
	 */
	public function test_auth_pages_are_served_without_jquery(): void
	{
		foreach ([route("login"), route("register")] as $url) {
			$response = $this->get($url);

			$response->assertOk();
			$response->assertDontSee("jquery", false);
			$response->assertSee("assets/js/bootstrap.bundle.min.js", false);
		}
	}

	public function test_no_project_script_initialises_datatables_through_jquery(): void
	{
		foreach ($this->projectScriptSources() as $path => $contents) {
			$this->assertDoesNotMatchRegularExpression(
				'/\$\([^)]*\)\s*\.\s*DataTable\s*\(/',
				$contents,
				"$path still initialises DataTables through the jQuery bridge",
			);
			$this->assertStringNotContainsString("\$.fn.dataTable", $contents, "$path still reaches DataTables through jQuery");
			$this->assertStringNotContainsString("\$.fn.DataTable", $contents, "$path still reaches DataTables through jQuery");
		}
	}

	public function test_views_initialise_datatables_through_the_standalone_global(): void
	{
		$initialising_views = array_filter($this->projectScriptSources(), fn ($contents) => str_contains($contents, "DataTable("));

		$this->assertCount(13, $initialising_views);
		foreach ($initialising_views as $path => $contents) {
			$this->assertMatchesRegularExpression('/new DataTable\(["\'][^"\']+["\'], \{/', $contents, "$path does not use the standalone constructor");
		}
	}

	public function test_the_users_list_renders_the_standalone_datatables_calls(): void
	{
		$response = $this->actingAs($this->makeUser(1))->get(route("users.index"));

		$response->assertOk();
		$response->assertSee('new DataTable("table", {', false);
		$response->assertSee("DataTable.render.text()", false);
		$response->assertSee("DataTable.render.number()", false);
	}

	/**
	 * The rest of the application still drives modals, ajax and the DOM through
	 * jQuery, so the main layout has to keep loading it.
	 */
	public function test_the_main_layout_still_loads_jquery(): void
	{
		$response = $this->actingAs($this->makeUser(1))->get(route("users.index"));

		$response->assertOk();
		$response->assertSee("assets/js/jquery-3.6.3.min.js", false);
	}
}
