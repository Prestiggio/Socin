<?php
namespace Ry\Socin\Http\Controllers;

use App\Http\Controllers\Controller;

class PublicController extends Controller
{
	public function getPopup() {
		return view("rysocin::auth.dialogs.login");
	}	
}