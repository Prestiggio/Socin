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
	Route::get("ry/socin/json/linking", "JsonController@getLinking");
	Route::post("ry/socin/json/email", "JsonController@postEmail");
	Route::post("ry/socin/json/hello", "JsonController@postHello");
	Route::post("ry/socin/json/payload", "JsonController@postPayload");
	Route::post("ry/socin/json/start", "JsonController@postStart");
	Route::post("ry/socin/json/text", "JsonController@postText");
});

Route::group(["middleware" => ["web", "auth", "admin"]], function(){
	Route::post("ry/socin/admin/delete", "AdminController@postDelete");
	Route::post("ry/socin/admin/delete-node", "AdminController@postDeleteNode");
	Route::post("ry/socin/admin/submit", "AdminController@postSubmit");
});

Route::group(["middleware" => "web"], function(){
	Route::get("ry/socin/popup", "PublicController@getPopup");
});
