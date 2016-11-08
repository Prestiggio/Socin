<?php
namespace Ry\Socin\Core;

use Facebook\Facebook;
use Ry\Socin\Http\Middleware\Socauth;
use Ry\Socin\Core\BaseConnector;

class BaseSocial
{
	protected $HANDLER = BaseTokenHandler::class;
	protected $MIDDLEWARE = Socauth::class;
	protected $CONNECTOR = BaseConnector::class;
	public $ID = "rysocin";
	
	/**
	 *
	 * @var BaseTokenHandler
	 */
	protected $h;
	
	protected $c;
	
	public $theme = "md";
	
	public function __construct($params) {
		$h = $this->HANDLER;
		$this->h = new $h();
		$c = $this->CONNECTOR;
		$this->c = new $c();
		$this->h->register("facebook", function($handler)use($params){
			return new Facebook($params["facebook"]);
		});
	}
	
	public function setupMiddleware() {
		app('router')->middleware($this->ID, $this->MIDDLEWARE);
	}
	
	/**
	 *
	 * @return \Ry\Fbform\Core\LaravelTokenHandler
	 */
	public function getHandler() {
		return $this->h;
	}
	
	public function getFacebook() {
		return $this->h->app("facebook");
	}
	
	public function getConnector() {
		return $this->c;
	}
	
	public function handle($request, $next, $guard = null) {
		$route = $request->route()->getAction();
		if(isset($route["controller"]) && (preg_match("/@getIndex$/", $route["controller"])
				|| preg_match("/@getLogin$/", $route["controller"])
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
	
	protected function pre_handle($request, $next, $guard = null) {
		
	}
}