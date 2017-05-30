<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
class Facebookusersession extends Model
{
	protected $table = "ry_socin_facebookusersessions";
	
	public function owner() {
		return $this->belongsTo("Ry\Socin\Models\Facebookuser", "facebookuser_id");
	}
}