<?php 
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
use Ry\Socin\Bot\Form;
use Ry\Socin\Models\Bot;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class BotFormField extends Model
{
	protected $table = "ry_socin_botformfields";
	
	protected $fillable = ["server_output", "user_input", "value"];
	
	public function form() {
		return $this->belongsTo("\Ry\Socin\Models\BotForm", "form_id");
	}
	
	public function handle($request) {
		$server_output = json_decode($this->server_output, true);
		try {
			list($controller, $method) = explode("@", $server_output["expect"]);
			$form = app($controller)->$method($request);
			if(isset($form->value)) {
				$value = $form->value;
				$model = $server_output["model"];
				$keys = explode(".", $model);
				$key = array_pop($keys);
				$model = implode(".", $keys);
				Bot::gotField(Form::dot2ar($model, function(&$ar) use ($key, $value){
					$ar[$key] = $value;
				}), $form);
			}
			return $form;
		}
		catch(\Exception $exception) {
			Log::error($exception);
		}
	}
}
?>