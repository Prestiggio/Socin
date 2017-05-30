<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class Facebookuser extends Model{		
	
	protected $table = "ry_socin_facebookusers";
	
	public function owner() {
		return  $this->belongsTo("App\User", "user_id");
	}
	
	public function sessions() {
		return $this->hasMany("Ry\Socin\Models\Facebookusersession", "facebookuser_id");
	}
}
