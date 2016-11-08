<?php
namespace Ry\Socin\Core;

use Ry\Socin\Core\Interfaces\ConnectorInterface;

abstract class BaseConnector implements ConnectorInterface
{
	const SESSIONKEY = "AccessToken";
	
	protected static $myself;
	
	protected $session, $tokenHandler;
	
	public $app = [];
	
	public function __construct($tokenHandler) {
		$this->tokenHandler = $tokenHandler;
	}
	
	/**
	 * 
	 * 
	 * @return \Ry\Socin\Core\BaseConnector
	 */
	public static function instance($handler) {
		if(!self::$myself) {
			self::$myself = new static($handler);
		}
		return self::$myself;
	}
	
	public function hasSession($accessToken = null) {
		return !is_null($this->session) && $this->session == $accessToken;
	}
	
	public function setSession($session = null) {
		$this->session = $session;
	}
	
	public function getSession() {
		return $this->session;
	}
}
