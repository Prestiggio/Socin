<?php 
namespace Ry\Socin\Bot;

use Lang, App;

class Menu
{
	private $children = [];
	private $title;
	private $conf = [
			"type" => "postback"
	];
	
	public function __construct($title, $payload=null) {
		$this->title = $title;
		if(is_array($payload) && isset($payload["url"])) {
			$this->conf["type"] = "web_url";
			$this->conf["url"] = $payload["url"];
			
			if(isset($payload["height"]))
				$this->conf["webview_height_ratio"] = $payload["height"]; //full, compact, tall
		}
		elseif(isset($payload)) {
			if(is_string($payload))
				$this->conf["payload"] = $payload;
			else
				$this->conf["payload"] = json_encode($payload);
		}	
	}
	
	public function addChild($menu) {
		$this->children[] = $menu;
	}
	
	public function toArray($lang) {		
		$ar = $this->conf;
		if(count($this->children)>0) {
			$ar["type"] = "nested";
			unset($ar["url"]);
			unset($ar["payload"]);
			unset($ar["webview_height_ratio"]);
			$ar["call_to_actions"] = [];
			if(count($this->children)<=5) {
				foreach ($this->children as $child) {
					$ar["call_to_actions"][] = $child->toArray($lang);
				}
			}
			else {
				$ellipse = new Menu("rysocin::bot.more", ["action" => "more_menu_" . str_slug($this->title)]);
				$ar["call_to_actions"][] = $this->children[0]->toArray($lang);
				$ar["call_to_actions"][] = $this->children[1]->toArray($lang);
				$ar["call_to_actions"][] = $this->children[2]->toArray($lang);
				$ar["call_to_actions"][] = $this->children[3]->toArray($lang);
				$ar["call_to_actions"][] = $ellipse->toArray($lang);
			}
		}
		$ar["title"] = Lang::get($this->title, [], $lang);
		if(strlen($ar["title"])>30)
			$ar["title"] = substr($ar["title"], 0, 27) . "...";
		return $ar;
	}
}