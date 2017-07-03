<?php 
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
use Ry\Socin\Bot\Form;
use Ry\Socin\Models\Bot;

class BotFormField extends Model
{
	protected $table = "ry_socin_botformfields";
	
	protected $fillable = ["server_output", "user_input", "value"];
	
	public function form() {
		return $this->belongsTo("\Ry\Socin\Models\BotForm", "form_id");
	}
	
	public function handle($message, $payload) {
		$server_output = json_decode($this->server_output, true);
		try {
			list($controller, $method) = explode("@", $server_output["expect"]);
			$ret = app($controller)->$method($message, $payload);
			if(isset($ret["value"])) {
				$value = $ret["value"];
				$model = $server_output["model"];
				$keys = explode(".", $model);
				$key = array_pop($keys);
				$model = implode(".", $keys);
				Bot::gotField(Form::dot2ar($model, function(&$ar) use ($key, $value){
					$ar[$key] = $value;
				}), $ret["form"]);
				return $ret["form"];
			}
		}
		catch(\Exception $exception) {
			
		}
		return [];
	}
}
?>