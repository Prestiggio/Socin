<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocinBotforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_botforms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("bot_id", false, true);
            $table->text("action");
            $table->text("form"); //the message payload shown as list in index
            $table->boolean("is_indexed")->default(true);
            $table->boolean("is_full")->default(false);
            $table->text("value")->nullable();
            $table->text("submitted")->nullable();
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
        Schema::drop('ry_socin_botforms');
    }
}
