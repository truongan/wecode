<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\my_helpers;
use App\Models\Setting;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
class ip_white_listing
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$pass = false;

		if (in_array(Auth::user()->role->name, ['admin', 'head_instructor']))
			$pass = true;
		else foreach (preg_split('/\s+/', Setting::get('ip_white_list', '0.0.0.0/0')) as $range){
			if (my_helpers::ip_in_range($request->ip(), $range)  ) $pass = true;
		}
		if ($pass == false){
			Log::critical('user {username} login from {ip} outside white list: {white_list}'
				, ['username' => Auth::user()->username, 'ip' => $request->ip(), 'white_list' => Setting::get('ip_white_list')]
			);
			abort(403, "Access from unauthorized network cannot proceed beyond this point");
		}
		return $next($request);
	}
}
