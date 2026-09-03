<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DataTablesUpgradeTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "datatables_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	public function test_package_manifest_pins_datatables_3(): void
	{
		$manifest = json_decode(file_get_contents(base_path("package.json")), true);

		$this->assertStringStartsWith("^3.", $manifest["dependencies"]["datatables.net"]);
		$this->assertStringStartsWith("^3.", $manifest["dependencies"]["datatables.net-bs5"]);
	}

	public function test_synced_bundle_is_datatables_3_with_bootstrap5_integration(): void
	{
		$script = public_path("assets/DataTables/datatables.min.js");
		$this->assertFileExists($script);

		$contents = file_get_contents($script);
		$this->assertMatchesRegularExpression("/DataTables 3\.\d+\.\d+/", $contents);
		$this->assertStringContainsString("DataTables Bootstrap 5 integration", $contents);
		$this->assertStringContainsString("dt-bootstrap5", $contents);
	}

	/**
	 * The Blade views drive DataTables through jQuery ($("table").DataTable()) and
	 * the render helpers ($.fn.dataTable.render.text/number), which DataTables 3
	 * only wires up when jQuery is already on the page.
	 */
	public function test_synced_bundle_keeps_the_jquery_entry_points_the_views_use(): void
	{
		$contents = file_get_contents(public_path("assets/DataTables/datatables.min.js"));

		$this->assertStringContainsString("fn.dataTable", $contents);
		$this->assertStringContainsString("fn.DataTable", $contents);
		$this->assertMatchesRegularExpression('/\.jQuery\s*&&/', $contents);
	}

	public function test_synced_stylesheet_is_the_bootstrap5_theme(): void
	{
		$stylesheet = public_path("assets/DataTables/datatables.min.css");
		$this->assertFileExists($stylesheet);

		$contents = file_get_contents($stylesheet);
		$this->assertStringContainsString("dt-container", $contents);
		$this->assertStringContainsString("--dt-row_background-selected", $contents);
	}

	public function test_users_list_page_loads_the_synced_bundle(): void
	{
		$response = $this->actingAs($this->makeUser(1))->get(route("users.index"));

		$response->assertOk();
		$response->assertSee("assets/DataTables/datatables.min.js", false);
		$response->assertSee("assets/DataTables/datatables.min.css", false);
	}

	/**
	 * DataTables 3 sends the same server-side processing payload as version 2, so
	 * the users.data endpoint must keep honouring search, ordering and paging.
	 */
	public function test_users_data_endpoint_answers_the_datatables_3_request_protocol(): void
	{
		$admin = $this->makeUser(1);
		$target = $this->makeUser(1);

		$response = $this->actingAs($admin)->getJson(
			route("users.data") .
				"?" .
				http_build_query([
					"draw" => 7,
					"start" => 0,
					"length" => 50,
					"search" => ["value" => $target->username, "regex" => "false"],
					"order" => [["column" => 1, "dir" => "desc", "name" => "username"]],
					"columns" => [
						["data" => "id", "name" => "id", "searchable" => "false", "orderable" => "true"],
						["data" => "username", "name" => "username", "searchable" => "true", "orderable" => "true"],
					],
				]),
		);

		$response->assertOk();
		$response->assertJsonPath("draw", 7);
		$response->assertJsonPath("recordsFiltered", 1);
		$response->assertJsonPath("data.0.username", $target->username);
	}

	public function test_users_data_endpoint_is_forbidden_for_non_admins(): void
	{
		$this->actingAs($this->makeUser(4))->getJson(route("users.data"))->assertForbidden();
	}
}
