<?php

namespace Tests\Feature;

use App\Http\Middleware\ip_white_listing;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class IpWhiteListingTest extends TestCase
{
	use DatabaseTransactions;

	private function makeUser(int $roleId): User
	{
		return User::create([
			"username" => "ip_white_list_test_" . uniqid(),
			"email" => uniqid() . "@example.test",
			"password" => bcrypt("password"),
			"role_id" => $roleId,
		]);
	}

	private function setWhiteList(string $whiteList): void
	{
		Setting::updateOrCreate(["key" => "ip_white_list"], ["value" => $whiteList]);
		Setting::set("ip_white_list", $whiteList);
	}

	/**
	 * @param  array<int, string>  $forwardedIps
	 */
	private function runMiddleware(User $user, string $remoteAddr, array $forwardedIps = []): int
	{
		Auth::login($user);
		$server = ["REMOTE_ADDR" => $remoteAddr];
		if ($forwardedIps !== []) {
			$server["HTTP_X_FORWARDED_FOR"] = implode(", ", $forwardedIps);
		}
		$request = Request::create("/", "GET", [], [], [], $server);
		$request->setTrustedProxies([$remoteAddr], Request::HEADER_X_FORWARDED_FOR);

		try {
			return (new ip_white_listing())->handle($request, fn () => response("ok"))->getStatusCode();
		} catch (HttpException $e) {
			return $e->getStatusCode();
		}
	}

	public function test_passes_when_any_forwarded_ip_is_in_white_list(): void
	{
		$this->setWhiteList("10.0.0.0/8");
		$student = $this->makeUser(4);

		$status = $this->runMiddleware($student, "172.16.0.1", ["10.1.2.3", "192.168.1.5"]);

		$this->assertSame(200, $status);
	}

	public function test_passes_when_direct_ip_is_in_white_list(): void
	{
		$this->setWhiteList("192.168.1.0/24");
		$student = $this->makeUser(4);

		$status = $this->runMiddleware($student, "192.168.1.20");

		$this->assertSame(200, $status);
	}

	public function test_blocks_when_no_ip_is_in_white_list(): void
	{
		$this->setWhiteList("10.0.0.0/8");
		$student = $this->makeUser(4);

		$status = $this->runMiddleware($student, "172.16.0.1", ["8.8.8.8", "192.168.1.5"]);

		$this->assertSame(403, $status);
	}

	public function test_admin_bypasses_white_list(): void
	{
		$this->setWhiteList("10.0.0.0/8");
		$admin = $this->makeUser(1);

		$status = $this->runMiddleware($admin, "8.8.8.8");

		$this->assertSame(200, $status);
	}
}
