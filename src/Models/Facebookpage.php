<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class Facebookpage extends Model{		
	
	protected $table = "ry_socin_facebookpages";
	
	public function botusers() {
		return $this->hasMany("Ry\Socin\Models\Bot", "page_id");
	}
	
}
