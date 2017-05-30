<?php
namespace Ry\Socin\Http\Controllers;

use Session, Auth;
use App\Http\Controllers\Controller;
use Ry\Socin\Core\BaseConnector;
use Illuminate\Filesystem\Filesystem;
use LaravelLocalization;
use Illuminate\Support\Facades\View;

class SocialController extends Controller
{	
	//must ocverride it
	protected $id = "socin";
	
	public function __construct() {
		$this->middleware(app($this->id)->middlewarename)->except([
				"getIndex",
				"getLogin",
				"postIndex",
				"getRefreshtoken",
				"getHaslogout"
		]);
		
		View::share("facebook", [
				"appId" => app($this->id)->getFacebook()->getApp()->getId()
		]);
	}
	
	public function getRefreshtoken() {
		$connector = app($this->id)->getConnector();
		return ["access_token" => bcrypt($connector->getSession())];
	}
	
	public function getHaslogout() {
		Session::flush();
	}
	
	public function getIndex() {
		return $this->getColor("red");
	}
	
	public function postIndex() {
		return $this->getColor("red");
	}
	
	public function getLogin() {
		$theme = app($this->id)->theme;
		return view("$theme::login", ["redirect" => false]);
	}
	
	public function getRegister() {
		return Auth::user();
	}
}
