<?php
namespace Ry\Socin\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Ry\Socin\Core\BaseConnector;
use Illuminate\Filesystem\Filesystem;
use LaravelLocalization;

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
	
	public function getApp() {
		$theme = app($this->id)->theme;
		return view("$theme::canvas.app");
	}
	
	public function getColor($color)
	{
		$theme = app($this->id)->theme;
		$baseUrl = app($this->id)->homeUrl;
		return view("$theme::canvas.home", ['js' => json_encode([
				"appId" => app($this->id)->getFacebook()->getApp()->getId(),
				"lang" => "",
				"region" => LaravelLocalization::getCurrentLocaleRegional(),
				"modules" => ["ngMaterial", "ngRySocial"],
				"ngRoutes" => [
						"default" => $baseUrl,
						$baseUrl => "$baseUrl/app"
				],
				"scope" => ["email"],
				"refreshTokenUrl" => "$baseUrl/refreshtoken",
				"flushUrl" => "$baseUrl/haslogout",
				"homeUrl" => $baseUrl
		])]);
	}
	
	public function getLogin() {
		$theme = app($this->id)->theme;
		return view("$theme::login", ["redirect" => false]);
	}
}
