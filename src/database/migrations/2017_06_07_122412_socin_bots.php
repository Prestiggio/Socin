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
            $table->integer("page_id", false, true)->nullable();
            $table->integer("facebookuser_id", false, true)->nullable();
            $table->char("psid", 50);
            $table->char("first_name", 50)->nullable();
            $table->char("last_name", 50)->nullable();
            $table->text("profile_pic")->nullable();
            $table->char("locale", 7)->nullable();
            $table->integer("timezone")->nullable();
            $table->char("gender", 10)->nullable();
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
