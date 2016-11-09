<?php
namespace Ry\Socin\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Ry\Socin\Core\BaseConnector;
use Illuminate\Filesystem\Filesystem;

class SocialController extends Controller
{	
	protected $id = "socin";
	
	public function __construct() {
		$this->middleware(app($this->id)->middlewarename);
	}
	
	public function getRefreshtoken() {
		$connector = app($this->id)->getConnector();
		return ["access_token" => bcrypt($connector->getSession())];
	}
	
	public function getHaslogout() {
		Session::flush();
	}
	
	public function getNocolor() {
		return ["color" => "none"];
	}
	
	public function getIndex() {
		return $this->getColor("red");
	}
	
	public function postIndex() {
		return $this->getColor("red");
	}
	
	public function getColor($color)
	{
		$theme = app($this->id)->theme;
		return view("$theme::canvas.home", ['js' => json_encode([
				"modules" => ["ngMaterial", "ngRySocial"],
				"ngRoutes" => [
						"default" => "/jostyle",
						"/jostyle" => "/jostyle/app",
						"/jostyle/photos" => "/jostyle/appphotos",
						"/jostyle/thanks" => "/jostyle/appthanks",
						"/jostyle/submit" => "/jostyle/appsubmit"
				],
				"scope" => ["email", "user_photos"]
		])]);
	}
	
	public function getLogin() {
		$theme = app($this->id)->theme;
		return view("$theme::login", ["redirect" => false]);
	}
}
