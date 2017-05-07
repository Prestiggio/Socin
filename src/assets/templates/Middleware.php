<?php
namespace Ry\Kto\Http\Middleware;

use Closure;

class Kto
{
	public function handle($request, Closure $next, $guard = null)
	{
		return app("kto")->handle($request, $next, $guard);
	}
}
