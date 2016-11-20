<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
class Facebookusersession extends Model
{
	public function owner() {
		return $this->belongsTo("Ry\Socin\Models\Facebookuser", "facebookuser_id");
	}
}