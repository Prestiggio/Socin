<?php
namespace Ry\Socin\Http\Controllers;

use Session;
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
		$this->middleware(app($this->id)->middlewarename);
		
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
		$routes = app($this->id)->routes;
		$baseUrl = $routes["base"];
		$ngRoutes = [
				"default" => $baseUrl,
				$baseUrl => "$baseUrl/app"
		];
		foreach ($routes["ajax"] as $route) {
			$ngRoutes["$baseUrl/$route"] = "$baseUrl/app$route";
		}
		return view("$theme::socin.canvas", ['js' => json_encode([
				"appId" => app($this->id)->getFacebook()->getApp()->getId(),
				"lang" => "",
				"region" => LaravelLocalization::getCurrentLocaleRegional(),
				"modules" => ["ngMaterial", "ngRySocial"],
				"ngRoutes" => $ngRoutes,
				"scope" => ["email"],
				"refreshTokenUrl" => "$baseUrl/refreshtoken",
				"flushUrl" => "$baseUrl/haslogout",
				"homeUrl" => $baseUrl,
				"theme" => [
						"primary" => "indigo",
						"palette" => [
		                '50'=> 'ffebee',
		                '100'=> 'ffcdd2',
		                '200'=> 'ef9a9a',
		                '300'=> 'e57373',
		                '400'=> 'ef5350',
		                '500'=> 'f44336',
		                '600'=> 'e53935',
		                '700'=> 'd32f2f',
		                '800'=> 'c62828',
		                '900'=> 'b71c1c',
		                'A100'=> 'ff8a80',
		                'A200'=> 'ff5252',
		                'A400'=> 'ff1744',
		                'A700'=> 'd50000',
		                'contrastDefaultColor'=> 'light',    // whether, by default, text (contrast)
		                                                    // on this palette should be dark or light
		                'contrastDarkColors'=> ['50', '100', //hues which contrast should be 'dark' by default
		                    '200', '300', '400', 'A100'],
		                //'contrastLightColors': undefined    // could also specify this if default was 'dark'
            		]
				]
		])]);
	}
	
	public function getLogin() {
		$theme = app($this->id)->theme;
		return view("$theme::login", ["redirect" => false]);
	}
}
