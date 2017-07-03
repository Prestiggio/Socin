<?php
Route::group(["middleware" => "bot"], function(){
	Route::get("ry/socin/forms", "JsonController@listForms");
	Route::post("ry/socin/form/{form}/cancel", "JsonController@cancel");
	Route::post("ry/socin/form/{form}/result", "JsonController@result");
	Route::post("ry/socin/form/{form}/continue", "JsonController@continueForm");
	Route::post("ry/socin/form/{form}/delete", "JsonController@remove");
	Route::post("ry/socin/form/{form}", "JsonController@form");
	Route::controller("ry/socin/json", "JsonController");
});

