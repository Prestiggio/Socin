<?php
namespace Ry\Socin\Core;

class Session
{
	public static function get($key) {
		if(isset($_SESSION))
			return $_SESSION[$key];
	}
	
	public static function put($key, $value = null) {
		if(isset($_SESSION))
			$_SESSION[$key] = $value;
	}
}