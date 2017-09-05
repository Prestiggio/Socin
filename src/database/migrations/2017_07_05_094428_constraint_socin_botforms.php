<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintSocinBotforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_socin_botforms', function (Blueprint $table) {
        	$table->foreign("bot_id")->references("id")->on("ry_socin_bots")->onDelete("cascade");
        	$table->foreign("parent_id")->references("id")->on("ry_socin_botformfields")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_socin_botforms', function (Blueprint $table) {
        	$table->dropForeign("ry_socin_botforms_bot_id_foreign");
        	$table->dropIndex("ry_socin_botforms_bot_id_foreign");
        	$table->dropForeign("ry_socin_botforms_parent_id_foreign");
        	$table->dropIndex("ry_socin_botforms_parent_id_foreign");
        });
    }
}
