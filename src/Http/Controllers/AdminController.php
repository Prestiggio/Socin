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
use Ry\Socin\Models\FacebookSource;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;

use Auth;

class AdminController extends Controller
{
	public function __construct() {
		$this->middleware("admin");
	}
	
	public function postSubmit(Request $request) {
		$ar = $request->all();
		
		$user = Auth::user();
		
		Model::unguard();
		$data = [
			"editor_id" => $user->id,
			"name" => $ar["name"],
			"url" => $ar["url"],
			"endpoint" => $ar["endpoint"],
			"access_token" => $ar["access_token"],
		];
		
		$row = null;
		if(isset($ar["id"]))
			$row = FacebookSource::where("id", "=", $ar["id"])->first();
		
		if(!$row)
			$row = FacebookSource::create($data);
		else
			$row->update($data);
		
		Model::reguard();
	}
	
	public function postDelete(Request $request) {
		if(isset($ar["id"]))
			$row = FacebookSource::where("id", "=", $ar["id"])->delete();
	}
	
	public function postDeleteNode(Request $request) {
		Facebooknode::where("id", "=", $request->get("id"))->delete();
	}
}

?>