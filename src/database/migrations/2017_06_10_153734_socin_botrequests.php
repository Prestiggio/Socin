<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocinBotrequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_botrequests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("bot_id", false, true);
            $table->text("payload");
            $table->char("handler")->nullable();
            $table->integer("priority")->nullable();
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
        Schema::drop('ry_socin_botrequests');
    }
}
