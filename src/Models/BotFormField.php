<?php 
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class BotFormField extends Model
{
	protected $table = "ry_socin_botformfields";
	
	protected $fillable = ["server_output", "user_input", "value"];
	
	public function form() {
		return $this->belongsTo("\Ry\Socin\Models\BotForm", "form_id");
	}
}
?>