<?php
namespace Ry\Socin\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Ry\Socin\Core\BaseConnector;

class SocialController extends Controller
{
	protected $theme = "md";
	
	const ID = "socauth";
	
	protected $con = BaseConnector::class;
	
	public function __construct() {
		$this->middleware(static::ID);
	}
	
	public function getRefreshtoken() {
		$connector = $con::instance(app(static::ID)->getHandler());
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
		return view("$this->theme::canvas.home", ['js' => json_encode([
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
		return view("$this->theme::login", ["redirect" => false]);
	}
}