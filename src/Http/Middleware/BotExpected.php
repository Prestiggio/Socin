<?php

namespace Ry\Socin\Http\Middleware;

use Closure;
use Ry\Socin\Models\BotFormField;
use Ry\Socin\Models\Bot;

class BotExpected
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
    	if($request->has("field_id")) {
    		$field = BotFormField::where("id", "=", $request->get("field_id"))->first();
    		$field->user_input = json_encode($request->all());
    		$field->save();
    		
    		Bot::focus($field);
    	}
    	
        return $next($request);
    }
}
