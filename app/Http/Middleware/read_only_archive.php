<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class read_only_archive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('APP_ARCHIVED') == true){
            if (in_array( strtoupper($request->method()), ['PUT', 'DELETE']) 
                or $request->routeIs('*.create')
                or $request->routeIs('*.edit')
                or $request->routeIs('*.store')
                or $request->routeIs('*.destroy')
            ){
                abort(403, "The website is in archived mode");
            }
        }

        return $next($request);
    }
}
