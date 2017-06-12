<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model{		
	
	protected $table = "ry_socin_bots";
	
	protected $fillable = ["psid"];
	
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
}
