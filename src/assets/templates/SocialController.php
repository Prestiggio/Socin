<?php
namespace Ry\Kto\Http\Controllers;

use Ry\Socin\Http\Controllers\SocialController;
class PublicController extends SocialController
{
	protected $id = "kto";
	
	protected $palette = [
			"primary" => "yellow",
			"accent" => "pink",
			"warn" => "red",
			"background" => "white"
		];
}