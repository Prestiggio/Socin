<?php 
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
use Faker\Generator as Faker;

class BotForm extends Model
{
	protected $table = "ry_socin_botforms";
	
	protected $fillable = ["action", "form", "is_indexed"];
	
	public function owner() {
		return $this->belongsTo("Ry\Socin\Models\Bot", "bot_id");
	}
	
	public function fields() {
		return $this->hasMany("Ry\Socin\Models\BotFormField", "form_id");
	}
	
	public function getNameAttribute() {
		$j = json_decode($this->form, true);
		return $this->owner->first_name . " à bien rempli notre formulaire " . $j["title"];
	}
	
	public function getDescriptionAttribute() {
		$details = [];
		foreach ($this->fields as $field) {
			$server_output = json_decode($field->server_output, true);
			if(isset($server_output["expect"]) && $field->user_input=="")
				continue;
				
			if($field->user_input!="") {
				$value = json_decode($field->value, true);
				if(!isset($value["li"])) {
					$lis = [];
					foreach($value["json"] as $k => $v) {
						$lis = array_merge($lis, $this->compactLi($v, $k));
					}
					$value["li"] = $lis;
					$field->value = json_encode($value);
				}
				$details[] = implode("\n", $value["li"]);
			}
			$field->save();
		}
		
		return implode("\n", $details);
	}
	
	private function compactLi($value, $parent, $keySeparator=".", $presentator=" : ") {
		if(is_array($value)) {
			$ar = [];
			foreach($value as $k => $v) {
				$ar = array_merge($ar, $this->compactLi($v, $parent.$keySeparator.$k));
			}
			return $ar;
		}
		return [$parent.$presentator.$value];
	}
	
	public function getUrlAttribute() {
		$j = json_decode($this->submitted, true);
		if(!isset($j["url"])) {
			$j["url"] = action("\Ry\Socin\Http\Controllers\JsonController@result", ["form" => $this]);
		}
		return $j["url"];
	}
	
	public function getImageAttribute() {
		$j = json_decode($this->submitted, true);
		if(!isset($j["image"])) {
			$faker = new Faker();
			$j["image"] = $faker->imageUrl();
		}
		return $j["image"];
	}
}
?>