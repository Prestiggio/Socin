<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facebooknode extends Model
{
	use SoftDeletes;
	
	protected $table = "ry_socin_facebooknodes";
	
	protected $dates = ['deleted_at'];
	
	public function bots() {
		return $this->belongsToMany("\Ry\Socin\Models\Bot", "ry_socin_facebooknode_bots", "facebooknode_id", "bot_id");
	}
	
	public function source() {
		return $this->belongsTo("Ry\Socin\Models\FacebookSource", "source_id");
	}
}