<?php
Route::get('ry/socin-token', function(\Illuminate\Http\Request $request){
	return $request->get("short");
	$ch = curl_init("https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=691462271025098&client_secret=635f60e1510231ea5bb5cae9a3f60b47&fb_exchange_token=EAAJ04ZAsKP8oBANGbI3QsOpt83whsDq3DFXVTeLCCphlzM9v4eh1vvZCQvG4cpt5AITNhOirwlxGd3swagiUb40E7orjGaKKjnNZCZAKRWXZCynptvf1bX6MCb14afRbGVvIIWnLteS8Q6PwbgBQMunHjkLbE7ZAfMNtsoc8mYDWwg65JHGJkLzxp0u6maYrkZD");
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
