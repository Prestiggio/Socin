<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacebookSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ry_socin_facebook_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger("editor_id");
            $table->char("name", 50);
            $table->char("url");
            $table->text("endpoint");
            $table->text("access_token");
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
        Schema::drop('ry_socin_facebook_sources');
    }
}
