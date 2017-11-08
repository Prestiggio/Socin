<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
use Ry\Socin\Http\Controllers\JsonController;
use Illuminate\Support\Facades\Log;
use Ry\Socin\Bot\Form;
use Illuminate\Http\Request;

class Bot extends Model{		
	
	protected $table = "ry_socin_bots";
	
	protected $fillable = ["psid", "first_name", "last_name", "profile_pic", "locale", "timezone", "gender"];
	
	private static $bot;
	
	public function page() {
		return $this->belongsTo("Ry\Socin\Models\Facebookpage", "page_id");
	}

	public function nodes() {
		return $this->belongsToMany("\Ry\Socin\Models\Facebooknode", "ry_socin_facebooknode_bots", "bot_id", "facebooknode_id");
	}
	
	public function field() {
		return $this->belongsTo("Ry\Socin\Models\BotFormField", "lock_field_id");
	}
	
	public function forms() {
		return $this->hasMany("Ry\Socin\Models\BotForm", "bot_id");
	}
	
	public function referrals() {
		return $this->hasMany("Ry\Socin\Models\Referral", "bot_id");
	}
	
	public static function setCurrent($bot) {
		self::$bot = $bot;
	}
	
	public static function current() {
		return self::$bot;
	}
	
	public function unlock() {
		$this->lock_field_id = null;
		$this->save();
	}
	
	public function lock($field) {
		$this->lock_field_id = $field->id;
		$this->save();
	}
	
	private function getAction($message) {
		if(isset($message["postback"]["payload"])) {
			try {
				return json_decode($message["postback"]["payload"], true);
			}
			catch(\Exception $e) {
				Log::error($e);
				return [
						"action" => $message["postback"]["payload"]
				];
			}
		}
		elseif(isset($message["message"]["quick_reply"]["payload"])) {
			try {
				return json_decode($message["message"]["quick_reply"]["payload"], true);
			}
			catch(\Exception $e) {
				Log::error($e);
				return [
						"action" => $message["message"]["quick_reply"]["payload"]
				];
			}
		}
	}
	
	public function next($message) {
		Log::info($message);
		if($this->field) {
			return array_merge($this->field->handle($message, []), $this->field->form->output());
		}
		else {
			try {
				$payload = $this->getAction($message);
				list($controller, $action) = explode("@", $payload["action"]);
				$request = Request::create("/", "POST", [
						"message" => $message,
						"payload" => $payload
				], [], [], [
						"CONTENT_TYPE" => "application/json"
				]);
				return app($controller)->$action($request);
			}
			catch(\Exception $e) {
				Log::error($e);
				return $this->index($message);
			}
			return $this->index($message);
		}
	}
	
	private function index($message) {
		$form = $this->forms()->where("name", "=", "index")->first();
		if(!$form) {
			$f = new Form("Accueil", null, true, "index");
			$f->select("Que veux-tu faire " . self::$bot->first_name . "?", [
					"Retour au menu" => JsonController::class . "@listForms"
			]);
			$form = $f->getForm();
		}
		return $form->output();
	}
	
	public static function gotField($value) {
		self::$bot->field->value = json_encode([
				"json" => $value
		]);
		self::$bot->field->save();
		self::$bot->unlock();
		return self::$bot->next();
	}
	
	public static function send($to, $form) {
		$ch = curl_init("https://devkipa.amelior.mg/social/send?&t=".time());
		curl_setopt($ch, CURLOPT_POST, true);
		$data = json_encode([
				"message" => $form->getFields(),
				"recipient" => $to
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		 'Content-Type: application/json',
		'Content-Length: ' . strlen($data))
		);
		curl_exec($ch);
		curl_close($ch);
	}
	
}
