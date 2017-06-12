<?php 
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class BotForm extends Model
{
	protected $table = "ry_socin_botforms";
	
	protected $fillable = ["name", "form"];
	
	public function owner() {
		return $this->belongsTo("Ry\Socin\Models\Bot", "bot_id");
	}
}
?>