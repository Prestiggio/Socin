<?php 
namespace Ry\Socin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Facebook\Facebook;
use Illuminate\Support\Facades\Log;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Ry\Socin\Models\Facebookpage;
use Ry\Socin\Models\Facebookuser;
use Ry\Socin\Models\Bot;
use Ry\Socin\Models\BotForm;
use Ry\Socin\Models\FacebooknodeBot;
use Ry\Socin\Models\Facebooknode;
use Ry\Socin\Exceptions\BotFormatException;
use Ry\Socin\Bot\Form;

use Illuminate\Support\Collection;

class JsonController extends Controller
{
	public function form(BotForm $f) {
		//payload called when button in index menu is clicked
		//returns the history of a form, and adding edit buttons
		//create first the detailed representation of the history
		$form = new Form(null, $f);
		
		if(!$f->is_full && $f->description!="")
			$form->info($f->description);
		
		if($f->is_full) {
			$form->hliste(new Collection([$f]));
			$actions = [
				"revenir au menu" => action("\Ry\Socin\Http\Controllers\JsonController@postStart"), //amboary menu natif,
				"supprimer" => action("\Ry\Socin\Http\Controllers\JsonController@remove", ["form" => $f])
			];
		}
		else {
			$actions = [
				"continuer" => action("\Ry\Socin\Http\Controllers\JsonController@continueForm", ["form" => $f]),
				"une autre fois" => action("\Ry\Socin\Http\Controllers\JsonController@postStart"),
				"supprimer" => action("\Ry\Socin\Http\Controllers\JsonController@cancel", ["form" => $f])
			];
		}
		
		$form->tiselect("Que veux tu faire ?", $actions);
		return $form;
	}
	
	public function remove(BotForm $f) {
		$f->is_indexed = false;
		$f->save();
		
		$form = new Form(null, null, false);
		$form->info("Le formulaire a été supprimé avec succès !");
		return $form;
	}
	
	public function cancel(BotForm $f) {
		foreach($f->fields as $field)
			$field->delete();
		
		$f->delete();
		
		$form = new Form(null, null, false);
		$form->info("Le formulaire a été supprimé avec succès !");
		return $form;
	}
	
	public function listForms() {
		$bot = Bot::current();
		$buttons = [];
		$forms = $bot->forms()->where("is_indexed", "=", true)->get();
		foreach($forms as $form) {
			$buttons[] = json_decode($form->form, true);
		}
		if(count($buttons)>0) {
			return [[
					"attachment" => [
							"type" => "template",
							"payload" => [
									"template_type" => "button",
									"text" => "Rappel",
									"buttons" => $buttons
							]
					]
			]];
		}
		
		$form = new Form(null, null, false);
		$form->info("Rien de neuf " . $bot->first_name . "!");
		return $form;
	}
	
	public function postStart(Request $request) {
		$ar = $request->all();		
		$form = new Form("Bienvenue");
		$form->tiselect("Say hello !", [
				"Bonjour !" => action("\Ry\Socin\Http\Controllers\JsonController@postHello") . "?lang=fr",
				"Hi !" => action("\Ry\Socin\Http\Controllers\JsonController@postHello") . "?lang=en",
				"Kaiza !" => action("\Ry\Socin\Http\Controllers\JsonController@postHello") . "?lang=mg"
		]);
		$form->expect(action("\Ry\Socin\Http\Controllers\JsonController@postHello"), $errorHello);
		$form->where();
		return $form;
	}
	
	public function postHello(Request $request) {
		//return error -> rerun the expect logic
		//may return nothing -> success and go to next
		//may return form -> start new thread and join
		
		$form = new Form(null, null, false);
		
		if($request->has("lang")) {
			$lang = $request->get("lang");
			return Bot::gotField([
					"user" => [
							"profile" => [
									"languages" => $lang
							]
					]
			], $form);
		}
		return $form;
	}
	
	public function getLinking(Request $request) {
		$bot = Bot::where("psid", "=", $request->get("psid"))->first();
		$bot->account_linking_token = $request->get("account_linking_token");
		$bot->save();
		return redirect($request->get("redirect_uri")."&authorization_code=12345678");
	}
	
	public function send($ar) {
		//send mail here
		
		return $ar;
	}
	
	public function result(BotForm $f) {
		$j = json_decode($f->value, true);
		$this->makeTree($j);
	}
	
	private function makeTree($ar) {
		echo "<ul>";
		foreach ($ar as $k => $v) {
			echo "<li>" . $k . " : ";
			if(is_array($v)) {
				$this->makeTree($v);
			}
			else {
				echo $v;
			}
			echo "</li>";
		}
		echo "</ul>";
	}
	
	public function postText(Request $request) {
		$model = $request->get("model");
		$keys = explode(".", $model);
		$key = array_pop($keys);
		$model = implode(".", $keys);
		$arrequest = $request->all();
		$form = new Form(null, Bot::currentField()->form);
		if(isset($arrequest["message"]["text"])) {
			$value = $arrequest["message"]["text"];
			Bot::gotField(Form::dot2ar($model, function(&$ar) use ($key, $value){
				$ar[$key] = $value;
			}), $form);
		}
		return $form;
	}
	
	public function postEmail(Request $request) {
		/**
		 * @todo email validation
		 */
		$arrequest = $request->all();
		$form = new Form(null, Bot::currentField()->form);
		if(isset($arrequest["message"]["text"])) {
			$value = $arrequest["message"]["text"];
			$model = $request->get("model");
			$keys = explode(".", $model);
			$key = array_pop($keys);
			$model = implode(".", $keys);
			
			Bot::gotField(Form::dot2ar($model, function(&$ar) use ($key, $value){
				$ar[$key] = $value;
			}), $form);
		}
		return $form;
	}
	
	public function postPayload($payload) {
		$model = $payload["model"];
		$keys = explode(".", $model);
		$key = array_pop($keys);
		$model = implode(".", $keys);
		$value = $payload["value"];
		$form = new Form(null, Bot::current()->field->form);
		Bot::gotField(Form::dot2ar($model, function(&$ar) use ($key, $value){
			$ar[$key] = $value;
		}), $form);
		return $form;
	}
}

?>