<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocinFbnodesDropendpoint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_socin_facebooknodes', function (Blueprint $table) {
            $table->dropColumn("endpoint");
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
            $table->char("endpoint");
        });
    }
}
