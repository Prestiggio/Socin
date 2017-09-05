<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintRealestatefbnodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_realestate_fbnodes', function (Blueprint $table) {
        	$table->foreign("editor_id")->references("id")->on("users")->onDelete("cascade");
        	$table->foreign("facebooknode_id")->references("id")->on("ry_socin_facebooknodes")->onDelete("cascade");
        	$table->foreign("immobilier_id")->references("id")->on("ry_realestate_immobiliers")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_realestate_fbnodes', function (Blueprint $table) {
        	$table->dropForeign("ry_realestate_fbnodes_editor_id_foreign");
        	$table->dropIndex("ry_realestate_fbnodes_editor_id_foreign");
        	$table->dropForeign("ry_realestate_fbnodes_facebooknode_id_foreign");
        	$table->dropIndex("ry_realestate_fbnodes_facebooknode_id_foreign");
        	$table->dropForeign("ry_realestate_fbnodes_immobilier_id_foreign");
        	$table->dropIndex("ry_realestate_fbnodes_immobilier_id_foreign");
        });
    }
}
