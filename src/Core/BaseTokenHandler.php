<?php
namespace Ry\Socin\Core;

use Ry\Socin\Core\Interfaces\TokenHandlerInterface;
use Ry\Socin\Core\Interfaces\ConnectorInterface;

class BaseTokenHandler
{
	public $permissions;
	
	protected $connector;
	
	protected $app = [];
	
	public function register($singleton, $callback) {
		$this->app[$singleton] = $callback($this);
	}
	
	/**
	 * 
	 * @param ConnectionInterface $connector
	 */
	public function getAccessToken($connector) {
		return Session::get($connector->SESSIONKEY);
	}
	
	/**
	 * 
	 * @param unknown $accessToken
	 * @param ConnectorInterface $connector
	 * @return NULL
	 */
	public function setAccessToken($accessToken, $connector) {
		$this->connector = $connector;
		
		Session::put($connector->SESSIONKEY, $accessToken);
			
		$user = null;
		
		/*
		 * an user should be logged in here
		 * */
		
		return $user;
	}
	
	public function app($singleton) {
		return $this->app[$singleton];
	}
}
