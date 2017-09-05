<?php
Route::get('ry/socin-token', function(\Illuminate\Http\Request $request){
	$ch = curl_init("https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=".env("fb_id")."&client_secret=".env("fb_secret")."&fb_exchange_token=" . $request->get("short"));
	curl_exec($ch);
	curl_close($ch);
});

Route::group(["middleware" => "bot"], function(){
	Route::get("ry/socin/forms", "JsonController@listForms");
	Route::post("ry/socin/form/{form}/cancel", "JsonController@cancel");
	Route::post("ry/socin/form/{form}/result", "JsonController@result");
	Route::post("ry/socin/form/{form}/continue", "JsonController@continueForm");
	Route::post("ry/socin/form/{form}/delete", "JsonController@remove");
	Route::post("ry/socin/form/{form}", "JsonController@form");
	Route::controller("ry/socin/json", "JsonController");
});

Route::group(["middleware" => ["web", "auth", "admin"]], function(){
	Route::controller("ry/socin/admin", "AdminController");
});

Route::group(["middleware" => "web"], function(){
	Route::controller("ry/socin", "PublicController");
});
