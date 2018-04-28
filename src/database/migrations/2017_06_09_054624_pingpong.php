<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pingpong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_socin_bots', function (Blueprint $table) {
            $table->integer("botrequest_id", false, true)->after("psid")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_socin_bots', function (Blueprint $table) {
            //
        });
    }
}
