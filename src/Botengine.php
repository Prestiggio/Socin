<?php 
namespace Ry\Socin;

use Ry\Socin\Models\Bot;

class Botengine
{
	private $handlers = [];
	private $defaultHandler = "Ry\Socin\Http\Controllers\SocialController@botIndex";
	
	public function register($handlerClass) {
		$this->handlers[] = new $handlerClass();
	}
	
	public function handle($fb, $message) {
		$bot = Bot::where("psid", "=", $message["sender"]["id"])->first();
		foreach ($this->handlers as $handler) {
			$tag = $handler->handle($fb, $message);
			$bot->request = $tag["request"];
			$bot->expected = $tag["next"];
			$bot->save();
		}
	}
	
	public function getDefaultHandler() {
		return $this->defaultHandler;
	}
}
?>