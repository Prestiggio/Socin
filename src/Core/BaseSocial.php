<?php
namespace Ry\Socin\Core;

use Facebook\Facebook;
use Ry\Socin\Http\Middleware\Socauth;
use Ry\Socin\Core\BaseConnector;
use Ry\Socin\Core\BaseTokenHandler;
use Illuminate\Filesystem\Filesystem;
use Facebook\Exceptions\FacebookResponseException;
use Auth, Session;

class BaseSocial
{
	/**
	 *
	 * @var BaseTokenHandler
	 */
	public $id;
	protected $handler;
	protected $middleware;
	protected $connector;
	protected $controllerClass;
	public $middlewarename;	
	public $theme;
	public $routes;
	
	private $c;
	
	public function __construct(
			$id="rysocin", 
			$theme="rymd", 
			$routes=[
					"base" => "/example",
					"controller" => null,
					"ajax" => []
			],
			$params=["facebook" => ['app_id' => "701633913339293",
    						'app_secret' => "e83f04bb3c85985e555d40c9b670de3c",
    						'default_graph_version' => 'v2.8']],
			$middleware=Socauth::class,
			$connector=BaseConnector::class,
			$handler=BaseTokenHandler::class) {
		$this->id = $id;
		$this->routes = $routes;
		$this->theme = $theme;
		$this->middlewarename = $id."auth";
		$this->handler = new $handler($id);
		$this->c = $connector;
		$this->middleware = $middleware;
		$this->handler->register("facebook", function($handler)use($params){
			return new Facebook($params["facebook"]);
		});
	}
	
	public function routes() {
		$route = $this->routes["base"];
		$controllerClass = $this->routes["controller"];
		app("router")->get("/$route/tab/edit", "\\".$controllerClass."@getEdit");
		app("router")->get("/$route/{color}", "\\".$controllerClass."@getColor")->where("color", "(thanks|submit|green|photos|preview|ndbc)");
		app("router")->controller("/$route", "\\".$controllerClass);
	}
	
	public function setupMiddleware() {
		app('router')->middleware($this->middlewarename, $this->middleware);
	}
	
	public function getHandler() {
		return $this->handler;
	}
	
	public function getFacebook() {
		return $this->handler->app("facebook");
	}
	
	public function getConnector() {
		if(!$this->connector) {
			$connector = $this->c;
			$this->connector = new $connector($this->id);
		}
		return $this->connector;
	}
	
	public function handle($request, $next, $guard = null) {
		$route = $request->route()->getAction();
		if(isset($route["controller"]) && (preg_match("/@getIndex$/", $route["controller"])
				|| preg_match("/@getLogin$/", $route["controller"])
				|| preg_match("/@getColor$/", $route["controller"])
				|| preg_match("/@postIndex$/", $route["controller"])
				|| preg_match("/@getRefreshtoken$/", $route["controller"])
				|| preg_match("/@getHaslogout$/", $route["controller"]))) {
	
				}
				else {
					$ph = $this->pre_handle($request, $next, $guard);
					if($ph)
						return $ph;
				}
	
				$response = $next($request);
				/*$content = $response->getContent();
				 $response->setContent($content);*/
				return $response;
	}
	
	protected function pre_handle($request, $next, $guard = null)
	{
		$connector = $this->getConnector();
		
		if (Auth::guard($guard)->guest() || !Session::get($connector->sessionKey())) {
			$fbconnect = $connector->connect();

			if(!$fbconnect || isset($fbconnect["error"]))
				return view("$this->theme::login", ["redirect" => !is_null($fbconnect)]);
		}

		try {
			if(!$this->handler->grantedAll())
				return view("$this->theme::grant", ["permissions" => $this->handler->permissions->data]);
		}
		catch(FacebookResponseException $e) {
			
			if($e->getCode()==190) {
				Session::flush();
			}
			
			return view("$this->theme::login", ["redirect" => true]);
		}
	}
}