<?php
namespace Ry\Socin\Http\Controllers;

use App\Http\Controllers\Controller;

class SocialController extends Controller
{
	public function getIndex() {
		return view("rysocin::home");
	}
}
