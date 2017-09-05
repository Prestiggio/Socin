<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocinFbnodesReplacenamebysourceid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_socin_facebooknodes', function (Blueprint $table) {
            $table->dropColumn("name");
            $table->integer("source_id", false, true)->after("id");
            $table->foreign("source_id")->references("id")->on("ry_socin_facebook_sources")->onDelete("cascade");
            $table->unique(["source_id", "fbid"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_socin_facebooknodes', function (Blueprint $table) {
        	$table->dropForeign("ry_socin_facebooknodes_source_id_foreign");
        	$table->dropUnique("ry_socin_facebooknodes_source_id_fbid_unique");
            $table->char("name", 50);
            $table->dropColumn("source_id");
        });
    }
}
