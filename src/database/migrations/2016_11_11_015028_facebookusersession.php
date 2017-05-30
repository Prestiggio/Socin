<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Facebookusersession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_facebookusersessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("facebookuser_id", false, true);
            $table->char("appname");
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
        Schema::drop('ry_socin_facebookusersessions');
    }
}
