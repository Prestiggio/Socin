<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintSocinBotformfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_socin_botformfields', function (Blueprint $table) {
        	$table->foreign("form_id")->references("id")->on("ry_socin_botforms")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_socin_botformfields', function (Blueprint $table) {
            $table->dropForeign("ry_socin_botformfields_form_id_foreign");
            $table->dropIndex("ry_socin_botformfields_form_id_foreign");
        });
    }
}
