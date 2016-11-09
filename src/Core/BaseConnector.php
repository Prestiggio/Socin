<?php
namespace Ry\Socin\Core;

use Ry\Socin\Core\Interfaces\ConnectorInterface;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Filesystem\Filesystem;

class BaseConnector implements ConnectorInterface
{
	public $id;
	
	protected $session;
	
	public $app = [];
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function sessionKey() {
		return $this->id."AccessToken";
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
	
	public function connect() {
		$this->session = app($this->id)->getHandler()->getAccessToken($this);
		
		$helper = app($this->id)->getHandler()->app("facebook")->getJavaScriptHelper();
		
		try {
			$accessToken = $helper->getAccessToken();
		} catch(FacebookResponseException $e) {
			// When Graph returns an error
		
			return ["error" => $e->getMessage()];
		} catch(FacebookSDKException $e) {
			// When validation fails or other local issues
			return ["error" => $e->getMessage()];
		}
		
		if (! isset($accessToken)) {
			return ["error" => 'No cookie set or no OAuth data could be obtained from cookie.'];
			exit;
		}
		
		if($this->hasSession($accessToken))
			return;
		
		// Logged in
		//var_dump($accessToken->getValue());
		
		return app($this->id)->getHandler()->setAccessToken($accessToken, $this);
	}
}
