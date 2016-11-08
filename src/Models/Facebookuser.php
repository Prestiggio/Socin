<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class Facebookuser extends Model{		
	public function owner() {
		return  $this->belongsTo("App\User", "user_id");
	}
}
