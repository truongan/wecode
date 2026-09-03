<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SlimSelectUpgradeTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "slimselect_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	private function syncedScript(): string
	{
		return file_get_contents(public_path("assets/slimselect/slimselect.js"));
	}

	public function test_package_manifest_pins_slim_select_4(): void
	{
		$manifest = json_decode(file_get_contents(base_path("package.json")), true);

		$this->assertStringStartsWith("^4.", $manifest["dependencies"]["slim-select"]);
	}

	/**
	 * The views load the UMD build with a plain <script> tag and then call
	 * `new SlimSelect(...)`, so the bundle has to keep exposing the global.
	 */
	public function test_synced_bundle_is_a_umd_build_exposing_the_slimselect_global(): void
	{
		$script = public_path("assets/slimselect/slimselect.js");
		$this->assertFileExists($script);

		$contents = $this->syncedScript();
		$this->assertStringContainsString(".SlimSelect=", $contents);
		$this->assertMatchesRegularExpression('/define\.amd/', $contents);
	}

	/**
	 * Version 4 is the first release with modal mode, so its presence is what
	 * separates the synced bundle from the version 3 one it replaced.
	 */
	public function test_synced_bundle_ships_the_version_4_modal_mode(): void
	{
		$this->assertStringContainsString("ss-modal-overlay", $this->syncedScript());
		$this->assertStringContainsString(
			"ss-modal-overlay",
			file_get_contents(public_path("assets/slimselect/slimselect.css")),
		);
	}

	/**
	 * Methods and settings the Blade views and add_assignments.js drive
	 * SlimSelect with; version 4 keeps all of them.
	 */
	public function test_synced_bundle_keeps_the_api_surface_the_views_use(): void
	{
		$contents = $this->syncedScript();

		foreach (["setData", "setSelected", "destroy", "addable", "afterChange"] as $member) {
			$this->assertStringContainsString($member, $contents, "SlimSelect no longer exposes {$member}");
		}

		foreach (["maxValuesShown", "closeOnSelect", "placeholderText", "keepOrder", "hideSelected"] as $setting) {
			$this->assertStringContainsString($setting, $contents, "SlimSelect no longer supports the {$setting} setting");
		}
	}

	/**
	 * problems/list.blade.php reaches instances back through the select element
	 * (`select.slim.destroy()`), and add_assignments.js renders options through
	 * the data-html attribute.
	 */
	public function test_synced_bundle_keeps_the_slim_back_reference_and_data_html_rendering(): void
	{
		$contents = $this->syncedScript();

		$this->assertMatchesRegularExpression('/\.slim\s*=/', $contents);
		$this->assertMatchesRegularExpression('/dataset\.html/', $contents);
	}

	/**
	 * The Bootstrap theming override styles SlimSelect purely through --ss-*
	 * custom properties on .ss-main / .ss-content, which version 4 still uses.
	 */
	public function test_bootstrap_override_targets_variables_the_bundle_still_defines(): void
	{
		$override = file_get_contents(public_path("assets/slimselect/an.slimselect.bootstrap.hack.css"));
		$stylesheet = file_get_contents(public_path("assets/slimselect/slimselect.css"));

		foreach (["--ss-primary-color", "--ss-bg-color", "--ss-border-color", "--ss-border-radius"] as $variable) {
			$this->assertStringContainsString($variable, $override);
			$this->assertStringContainsString($variable, $stylesheet, "SlimSelect 4 dropped {$variable}");
		}

		$this->assertStringContainsString(".ss-content .ss-list .ss-option", $stylesheet);
	}

	/**
	 * @return array<string, array{0: int, 1: string}>
	 */
	public static function slimSelectPageProvider(): array
	{
		return [
			"problems list" => [1, "problems.index"],
			"problems create" => [1, "problems.create"],
			"assignments list" => [1, "assignments.index"],
			"assignments create" => [1, "assignments.create"],
		];
	}

	#[\PHPUnit\Framework\Attributes\DataProvider("slimSelectPageProvider")]
	public function test_pages_load_the_synced_bundle(int $roleId, string $routeName): void
	{
		$response = $this->actingAs($this->makeUser($roleId))->get(route($routeName));

		$response->assertOk();
		$response->assertSee("assets/slimselect/slimselect.js", false);
		$response->assertSee("assets/slimselect/slimselect.css", false);
		$response->assertSee("assets/slimselect/an.slimselect.bootstrap.hack.css", false);
		$response->assertSee("new SlimSelect(", false);
	}
}
