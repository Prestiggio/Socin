<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocinBots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_bots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("page_id", false, true);
            $table->integer("facebookuser_id", false, true);
            $table->char("psid", 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ry_socin_bots');
    }
}
