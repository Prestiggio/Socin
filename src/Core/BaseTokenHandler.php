<?php
namespace Ry\Socin\Core;

use Ry\Socin\Core\Interfaces\TokenHandlerInterface;
use Ry\Socin\Core\Interfaces\ConnectorInterface;
use Auth, Session;
use Ry\Socin\Models\Facebookuser;
use App\User;
use Ry\Socin\Models\Facebookusersession;

class BaseTokenHandler
{
	public $permissions;
	
	protected $connector;
	
	protected $app = [];
	
	protected $id;
	
	public function __construct($id="rysocin") {
		$this->id = $id;
	}
	
	public function register($singleton, $callback) {
		$this->app[$singleton] = $callback($this);
	}
	
	/**
	 * 
	 * @param ConnectionInterface $connector
	 */
	public function getAccessToken($connector) {
		return Session::get($connector->sessionKey());
	}
	
	public function app($singleton) {
		return $this->app[$singleton];
	}
	
	public function setAccessToken($accessToken, $connector) {
		$this->connector = $connector;
	
		Session::put($this->connector->sessionKey(), $accessToken->getValue());
	
		$fbresponse = $this->app("facebook")->get("/me?fields=id,name,email,birthday,first_name,last_name,gender", Session::get($this->connector->sessionKey()));
	
		$graphUser = $fbresponse->getGraphUser();
	
		$fbuser = Facebookuser::where("fbid", "=", $graphUser['id'])->first();
	
		if(!$fbuser) {
			$user = User::where("email", "LIKE", $graphUser['email'])->first();
	
			if(!$user) {
				User::unguard();
				$user = User::create([
						"name" => $graphUser['name'],
						"email" => $graphUser['email'],
						"password" => bcrypt(str_random())
				]);
				User::reguard();
			}
	
			Facebookuser::unguard();
			$fbuser = Facebookuser::create([
					"user_id" => $user->id,
					"fbid" => $graphUser['id'],
					"access_token" => Session::get($this->connector->sessionKey()),
					"firstname" => $graphUser['first_name'],
					"lastname" => $graphUser['last_name'],
					"gender" => $graphUser['gender']
			]);
			Facebookuser::reguard();
		}
		
		Facebookusersession::unguard();
		$fbuser->sessions()->create([
			"appname" => $this->app("facebook")->getApp()->getId()
		]);
		Facebookusersession::reguard();
	
		Auth::login($fbuser->owner);
	
		return $fbuser->owner;
	}
	
	public function grantedAll() {
		$connector = app($this->id)->getConnector();
		$this->permissions = json_decode($this->app("facebook")->get("/me/permissions", Session::get($connector->sessionKey()))->getBody());
		$all = true;
		$perms = [];
		if($this->permissions && isset($this->permissions->data)) {
			foreach ($this->permissions->data as $perm) {
				$perms[$perm->permission] = $perm->status;
				$all &= ($perm->status == "granted");
			}
		}
		$ar = array_filter(["email", "user_photos"], function($item)use($perms){
			return !isset($perms[$item]);
		});
		foreach($ar as $a)
			$this->permissions->data[] = (object)["permission" => $a, "status" => "new"];
		return $all;
	}
}
