<?php
namespace Ry\Socin;

use Illuminate\Filesystem\Filesystem;
class App
{
	public function routes($socapproutes) {
		foreach ($socapproutes as $route => $controllerClass) {
			app("router")->get("/$route/tab/edit", "\\".$controllerClass."@getEdit");
			app("router")->get("/$route/{color}", "\\".$controllerClass."@getColor")->where("color", "(thanks|submit|green|photos|preview)");
			app("router")->controller("/$route", "\\".$controllerClass);
		}
	}
}
