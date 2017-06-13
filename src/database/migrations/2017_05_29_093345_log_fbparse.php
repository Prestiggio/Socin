<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LogFbparse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_facebooknodes', function (Blueprint $table) {
            $table->increments('id');
            $table->char("fbid");
            $table->datetime("fbcreated");
            $table->char("endpoint");
            $table->char("name", 50);
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
        Schema::drop('ry_socin_facebooknodes');
    }
}
