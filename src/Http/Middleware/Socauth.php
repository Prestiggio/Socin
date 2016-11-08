<?php

namespace Ry\Socin\Http\Middleware;

use Illuminate\Filesystem\Filesystem;
class Socauth
{
	public function handle($request, $next, $app)
	{
		if($app)
			return app($app)->handle($request, $next, $app);
		
		$response = $next($request);
		return $response;
	}
}
