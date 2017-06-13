<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RealestatePsidFbnode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_facebooknode_bots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("facebooknode_id", false, true);
            $table->integer("bot_id", false, true);
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
        Schema::drop('ry_socin_facebooknode_bots');
    }
}
