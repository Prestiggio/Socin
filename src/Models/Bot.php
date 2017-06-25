<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model{		
	
	protected $table = "ry_socin_bots";
	
	protected $fillable = ["psid", "first_name", "last_name", "profile_pic", "locale", "timezone", "gender"];
	
	private static $bot, $field;
	
	public function page() {
		return $this->belongsTo("Ry\Socin\Models\Facebookpage", "page_id");
	}

	public function nodes() {
		return $this->belongsToMany("\Ry\Socin\Models\Facebooknode", "ry_socin_facebooknode_bots", "bot_id", "facebooknode_id");
	}
	
	public function requests() {
		return $this->hasMany("Ry\Socin\Models\BotRequest", "bot_id");
	}
	
	public function currentrequest() {
		return $this->belongsTo("Ry\Socin\Models\BotRequest", "botrequest_id");
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
	
	public static function focus($field) {
		self::$field = $field;
	}
	
	public static function current() {
		return self::$bot;
	}
	
	public static function currentField() {
		return self::$field;
	}
	
	public static function gotField($value, &$form) {
		self::$field->value = json_encode([
				"json" => $value
		]);
		self::$field->save();
		$form->expect("");
		$form->append(app("Ry\Socin\Http\Controllers\JsonController")->continueForm(self::$field->form));
		return $form;
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
