<?php
namespace Ry\Socin;

use Illuminate\Filesystem\Filesystem;
class App
{
	public function routes($apps) {
		foreach($apps as $app) {
			app($app)->routes();
		}
	}
}
