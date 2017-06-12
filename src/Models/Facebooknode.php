<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
class Facebooknode extends Model
{
	protected $table = "ry_socin_facebooknodes";
	
	public function bots() {
		return $this->belongsToMany("\Ry\Socin\Models\Bot", "ry_socin_facebooknode_bots", "facebooknode_id", "bot_id");
	}
}