<?php 
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class BotRequest extends Model
{
	protected $table = "ry_socin_botrequests";
	
	protected $fillable = ["payload", "handler"];
	
	public function recipient() {
		return $this->belongsTo("Ry\Socin\Models\Bot", "bot_id");
	}
}
?>