<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstellationDescTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constellation_desc', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('constellation_id')->comment = "星座代號";
            $table->integer('constellation_lucky_id')->comment = "運勢代號";
            $table->string('luck_star')->comment = "星星數";
            $table->string('date')->comment = '日期';
            $table->longText('description')->comment = '說明內容';
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
        Schema::dropIfExists('constellation_desc');
    }
}
