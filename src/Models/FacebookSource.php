<?php

namespace Ry\Socin\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookSource extends Model
{
    protected $table = "ry_socin_facebook_sources";
    
    protected $with = ["editor"];
    
    public function editor() {
    	return $this->belongsTo("App\User", "editor_id");
    }
    
    public function nodes() {
    	return $this->hasMany("Ry\Socin\Models\Facebooknode", "source_id");
    }
}
