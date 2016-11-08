<?php
namespace Ry\Socin\Core;

use Ry\Socin\Core\Interfaces\ConnectorInterface;

class BaseConnector implements ConnectorInterface
{
	public $SESSIONKEY = "AccessToken";
	
	protected $session;
	
	public $app = [];
	
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
