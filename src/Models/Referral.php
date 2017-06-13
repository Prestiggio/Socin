<?php
namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;
class Referral extends Model
{
	protected $table = "ry_socin_botreferrals";
	
	protected $fillable = ["referral"];
	
	public function bot() {
		return $this->belongsTo("Ry\Socin\Models\Bot", "bot_id");
	}
}