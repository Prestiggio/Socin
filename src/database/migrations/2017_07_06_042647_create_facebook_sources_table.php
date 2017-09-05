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
            $table->integer("editor_id", false, true);
            $table->char("name", 50);
            $table->char("url");
            $table->text("endpoint");
            $table->text("access_token");
            $table->timestamps();
            
            $table->foreign("editor_id")->references("id")->on("users");
            $table->unique("name");
            $table->unique("url");
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
