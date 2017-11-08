<?php 
namespace Ry\Socin\Bot;

use Lang, App;

use Ry\Socin\Models\Bot;
use Ry\Socin\Http\Controllers\JsonController;

class Form
{
	private $fields = [];
	private $bot, $form;
	private $re = false;
	
	public function __construct($menu_title=null, $form=null, $save=true, $name=null) {		
		$this->bot = Bot::current();
		if($save) {
			if(!is_string($save))
				$save = JsonController::class . "@send";
			
			if(!isset($form)) {
				$this->form = $this->bot->forms()->create([
						"is_indexed" => isset($menu_title),
						"name" => $name,
						"action" => $save
				]);
				if(!isset($menu_title))
					$menu_title = "Feuille #" . $this->form->id;
				$this->form->form = json_encode([
						"type"=> "postback",
						"title" => $menu_title,
						"payload" => json_encode([
								"action" => JsonController::class . "@form",
								"id" => $this->form->id
						])
				]);
				$this->form->save();
			}
			else {
				$this->form = $form;
				$this->re = true;
			}
		}
		else {
			$this->re = true;
		}
	}
	
	public function getForm() {
		return $this->form;
	}
	
	private function save(&$field) {
		if(!$this->re) {
			if(isset($field["expect"]) && $field["expect"]=="")
				return;
			
			$f = $this->form->fields()->create([
				"server_output" => json_encode($field)
			]);
			
			if(isset($field["expect"]) && $field["expect"]!="") {
				//$field["expect"] = $field["expect"] . (preg_match("/\?/i", $field["expect"]) ? "&" : "?") . "field_id=" . $f->id;
				$f->server_output = json_encode($field);
				$f->save();
			}
		}
	}
	
	public function file($path) {
		$field = [
				"attachment" => [
						"type" => "file",
						"payload" => []
				]
		];
		if(preg_match("/http:\/\//i", $path) || preg_match("/https:\/\//i", $path))
			$field["attachment"]["payload"] = ["url" => $path];
		else
			$field["filedata"] = $path;
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function picture($path) {
		$field = [
				"attachment" => [
					"type" => "image",
					"payload" => []
			]
		];
		if(preg_match("/http:\/\//i", $path) || preg_match("/https:\/\//i", $path))
			$field["attachment"]["payload"] = ["url" => $path];
		else
			$field["filedata"] = $path;
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function video($path) {
		$field = [
				"attachment" => [
					"type" => "video",
					"payload" => []
				]
		];
		if(preg_match("/http:\/\//i", $path) || preg_match("/https:\/\//i", $path))
			$field["attachment"]["payload"] = ["url" => $path];
		else
			$field["filedata"] = $path;
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function audio($path) {
		$field = [
				"attachment" => [
					"type" => "audio",
					"payload" => []	
				]
		];
		if(preg_match("/http:\/\//i", $path) || preg_match("/https:\/\//i", $path))
			$field["attachment"]["payload"] = ["url" => $path];
		else
			$field["filedata"] = $path;
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function expect($action, $model) {
		$field = [
				"expect" => $action,
				"model" => $model
		];
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function select($textMessage, $choices) {
		$buttons = [];
		foreach ($choices as $title => $payload) {
			if(is_array($payload) && isset($payload["url"])) {
				$button = [
						"type"=> "web_url",
						"url"=> $payload["url"],
						"title" => $title
				];
				if(isset($payload["height"]))
					$button["webview_height_ratio"] = $payload["height"]; //full, compact, tall
			}
			elseif(is_string($payload)) {
				if(preg_match("/^[0-9\+\-\(\)\s\t\*\#]+$/i", $payload)) {
					$button = [
							"type" => "phone_number",
							"title" => $title,
							"payload" => $payload
					];
				}
				else {
					$button = [
							"type"=> "postback",
							"title" => $title,
							"payload" => $payload
					];
				}
			}
			else {
				$s = print_r($payload, true);
				if(strlen($s)>1000)
					$s = substr($s, 0, 997) . "...";
				$button = [
						"type"=> "postback",
						"title" => $title,
						"payload" => $s
				];
			}
			$buttons[] = $button;
		}
		$field = [
				"attachment" => [
						"type" => "template",
						"payload" => [
								"template_type" => "button",
								"text" => $textMessage,
								"buttons" => $buttons
						]
				]
		];
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function info($textMessage) {
		$field = ["text" => strlen($textMessage)>640 ? substr($textMessage, 0, 637)."..."  : $textMessage];
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function link($invitationMessage, $payload=null) {
		if(!isset($payload)) {
			$payload = json_encode([
					"action" => JsonController::class . "@getLinking",
					"params" => [
							"psid" => $this->bot->psid
					]
			]);
		}
		$field = [
				"attachment" => [
					"type" => "template",
					"payload" => [
						"template_type" => "button",
						"text" => $invitationMessage,
						"buttons" => [
							[
								"type"=> "account_link",
								"url"=> $payload
							]
						]
					]
				]
		];
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function vliste($structures, $pinFirst=true) {
		$elements = [];
		foreach($structures as $item) {				
			$elements[] = [
					"title" => strlen($item->name)>80 ? substr($item->name, 0, 77) . "..." : $item->name,
					"subtitle" => strlen($item->description)>80 ? substr($item->description, 0, 77) . "..." : $item->description,
					"image_url" => $item->image,
					"default_action" => [
							"type" => "web_url",
							"url" => $item->url,
							"webview_height_ratio" => "compact" //full, compact, tall
					],
					"buttons" => [ //1 ian an
							[
									"type"=> "postback",
									"title" => "Enregistrer",
									"payload" => $item->url . "?bot_action=save"
							]
					]];
		}
		
		$field = [
				"attachment" => [
						"type" => "template",
						"payload" => [
								"template_type" => "list",
								"top_element_style" => "large", //large(mobile only) ou compact
								"elements" => $elements,
								"buttons" => [
										[
												"title" => "Voir plus",
												"type" => "postback",
												"payload" => $structures->nextPageUrl()
										]
								]
						]
				]
		];
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function hliste($structures) {
		$elements = [];
		$i = 0;
		foreach($structures as $item) {
			if($i>=10) //tokn python no higerer an'ty ou
				break;
			
			$elements[] = [
				"title" => strlen($item->name)>80 ? substr($item->name, 0, 77) . "..." : $item->name,
				"subtitle" => strlen($item->description)>80 ? substr($item->description, 0, 77) . "..." : $item->description,
				"image_url" => $item->image,
				"buttons" => [ //3 ian an
					[
						"type"=> "web_url",
						"url"=> $item->url,
						"title" => "Voir +",
						"webview_height_ratio" => "compact" //full, compact, tall
					],
					[
						"type"=> "postback",
						"title" => "Enregistrer",
						"payload" => $item->url . "?bot_action=save"
					],
					[
						"type" => "element_share",
						"share_contents" => [
							"attachment" => [
								"type" => "template",
								"payload" => [
									"template_type" => "generic",
									"elements" => [[
										"title" => strlen($item->name)>80 ? substr($item->name, 0, 77) . "..." : $item->name,
										"subtitle" => strlen($item->description)>80 ? substr($item->description, 0, 77) . "..." : $item->description,
										"image_url" => $item->image,
										"default_action" => [
											"type" => "web_url",
											"url" => $item->url
										],
										"buttons" => [
											[
												"type"=> "web_url",
												"url"=> $item->url,
												"title"=> "Voir +"
											]
										]
									]]
								]
							]
						]
					]
				]];
			
			$i++;
		}
		
		$field = [
			"attachment" => [
				"type" => "template",
				"payload" => [
					"template_type" => "generic",
					"elements" => $elements
				]
			]
		];
		
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function tiselect($title, $choices) {
		$replies = []; //limited to 11
		foreach($choices as $label => $payload) {
			$reply = [
					"content_type" => "text",
					"title" => $label
			];
			
			if(is_array($payload)) {
				if(isset($payload["image"])) {
					$reply["image_url"] = $payload["image"];
				}
				$pl = json_encode($payload);
			}
			elseif(is_string($payload)) {
				$pl = $payload;
			}
			else {
				$pl = print_r($payload, true);
			}
			
			if(strlen($pl)>1000)
				$pl = substr($pl, 0, 997) . "...";
			
			$reply["payload"] = $pl;
			
			$replies[] = $reply;
		}
		
		$field = [
			"text" => $title,
			"quick_replies" => $replies
		];
		
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function where() {
		$field = [
			"attachment" => [
				"type" => "template",
				"payload" => [
					"template_type" => "generic",
					"elements" => [[
						"title" => "Breaking News: Record Thunderstorms",
						"subtitle" => "The local area is due for record thunderstorms over the weekend.",
						"image_url" => url("medias/img/gallery/gallery-1.jpg"),
						"buttons" => [ //3 ian an
							[
								"type"=> "web_url",
								"url"=> url("/") . "?psid=" . $this->bot->psid,
								"title" => "Rechercher sur le site",
								"webview_height_ratio" => "compact" //full, compact, tall
							],
							[
								"type"=> "postback",
								"title" => "Efa lasa",
								"payload" => "any e" //full, compact, tall
							],
							[
								"type" => "element_share",
								"share_contents" => [
									"attachment" => [
										"type" => "template",
										"payload" => [
											"template_type" => "generic",
											"elements" => [[
													"title" => "zanany Breaking News: Record Thunderstorms",
													"subtitle" => "zanany The local area is due for record thunderstorms over the weekend.",
													"image_url" => url("medias/img/gallery/gallery-1.jpg"),
													"default_action" => [
														"type" => "web_url",
														"url" => url("/?invited=za")
													],
													"buttons" => [
														[
															"type"=> "web_url",
															"url"=> url("/?invited=za"),
															"title"=> "Take Quiz"
														]
													]
												]]
											]
										]
									]
								]
							]
						]]
					]
				],
			"quick_replies" => [
				[
					"content_type" => "location"
				]
			]
		];
		
		$this->save($field);
		$this->fields[] = $field;
	}
	
	public function append($field) {
		if(is_array($field)) {
			$this->save($field);
			$this->fields[] = $field;
		}
		elseif($field instanceof Form) {
			$fields = $field->getFields();
			foreach ($fields as $f) {
				$this->fields[] = $f;
			}
		}
	}
	
	public function __toString() {
		if($this->form)
			return json_encode($this->form->output());
		return json_encode($this->fields);
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public static function dot2ar($dotNotation, $func) {
		if(!is_string($dotNotation))			
			$dotNotation = "";
		$keys = explode(".", $dotNotation);
		if(count($keys)>0 && strlen($keys[0])>0)
			$arr = [$keys[0] => false];
		else 
			$arr = [];
		self::__dot2ar($arr, $dotNotation, $func);
		return $arr;
	}
	
	private static function __dot2ar(&$ar, $dotNotation, $func) {
		$keys = explode(".", $dotNotation);
		if(count($keys)>0 && strlen($keys[0])>0) {
			foreach ($keys as $key) {
				$ar = &$ar[$key];
			}
		}
		$thear = [];	
		$func($thear);
		$ar = $thear;
	}
	
	public function set($dotNotation, $value) {
		$keys = explode(".", $dotNotation);
		$last = array_pop($keys);
		$this->form->fields()->create([
				"value" => json_encode(self::dot2ar(implode(".", $keys), function(&$ar) use ($last, $value){
					$ar[$last] = $value;
				}))
		]);
	}
	
	public function parent() {
		return $this->belongsTo("\Ry\Socin\Models\BotFormField", "parent_id");
	}
}
?>