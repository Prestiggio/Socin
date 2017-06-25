<?php

namespace Ry\Socin\Http\Middleware;

use Closure;
use Ry\Socin\Models\Bot;

class BotPsidRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$ar = $request->all();
    	
    	if(isset($ar["sender"]["id"])) {
    		$bot = Bot::where("psid", "=", $ar["sender"]["id"])->first();
    		if(!$bot) {
    			$bot = Bot::create([
    					"psid" => $ar["sender"]["id"]
    			]);
    		}
    		Bot::setCurrent($bot);
    	}
    	else {
    		$bot = Bot::where("id", "=", 1)->first();
    		Bot::setCurrent($bot);
    	}
    	
        return $next($request);
    }
}
