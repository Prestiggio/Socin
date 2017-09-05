<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocinBotFormFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_botformfields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("form_id", false, true);
            $table->text("server_output"); //ty le checken'ny pysocin ra expect ka null ny user_input
            $table->text("user_input")->nullable(); //ty ny input n'le user ra valid - aleo tahirizina ny payload feno $request->all()
            $table->text("value")->nullable(); //merge ex : json {immobilier.adresse:adiresy_value,display:controller@method na service@method, message:message_value, list:list_value}, immobilier adresse ville, immobilier addresse ville pays, cp...
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
        Schema::drop('ry_socin_botformfields');
    }
}
