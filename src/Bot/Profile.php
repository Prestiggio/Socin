<?php 
namespace Ry\Socin\Bot;

use LaravelLocalization, Lang, App;
use Ry\Socin\Bot\Menu;
use Illuminate\Http\Request;
use Ry\Socin\Http\JsonController;

class Profile
{
	private $greetings = [];
	private $menus = [];
	private $taponly = false;
	private $start_payload;
	
	public function __construct($start_payload = null, $taponly = false) {
		$this->taponly = $taponly;
		if(!isset($start_payload)) {
			$start_payload = json_encode([
					"action" => JsonController::class . "@postStart"
			]);
		}
		elseif(is_array($start_payload)) {
			$start_payload = json_encode($start_payload);
		}
		$this->start_payload = $start_payload;
	}
	
	public function greeting($transkey) {
		$ar = LaravelLocalization::getSupportedLocales();
		foreach ($ar as $d2 => $v) {
			if($d2=="fr") {
				$this->greetings[] = [
								"locale" => "default",
								"text" => Lang::get($transkey, [], $d2)
						];
			}
			$this->greetings[] = [
					"locale" => $v["regional"],
					"text" => Lang::get($transkey, [], $d2)
			];
		}
	}
	
	public function setup() {
		$menus = [];
		$ar = LaravelLocalization::getSupportedLocales();		
		foreach ($ar as $d2 => $v) {
			$armenus = [];
			if(count($this->menus)<=3) {
				foreach($this->menus as $menu) {
					$armenus[] = $menu->toArray($d2);
				}
			}
			else {
				$ellipse = new Menu("rysocin::bot.more", ["action" => "more_menu"]);
				$armenus[] = $this->menus[0]->toArray($d2);
				$armenus[] = $this->menus[1]->toArray($d2);
				$armenus[] = $ellipse->toArray($d2);
			}
			
			if($d2=="fr") {
				$menus[] = [
						"locale" => "default",
						"composer_input_disabled" => $this->taponly,
						"call_to_actions" => $armenus
				];
			}
			
			$menus[] = [
					"locale" => $v["regional"],
					"composer_input_disabled" => $this->taponly,
					"call_to_actions" => $armenus
			];
		}
		
		return [
				"get_started" => [
					"payload" => $this->start_payload
				],
				"greeting" => $this->greetings,
				"persistent_menu" => $menus
		];
	}
	
	public function menu($menu) {
		$this->menus[] = $menu;
	}
	
	public function handle(Request $request) {
		
	}
}
?>