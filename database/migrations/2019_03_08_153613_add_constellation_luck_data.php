<?php

use Illuminate\Database\Migrations\Migration;

class AddConstellationLuckData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = array(
            array(
                'title' => '整體運勢',
            ),
            array(
                'title' => '愛情運勢',
            ),
            array(
                'title' => '事業運勢',
            ),
            array(
                'title' => '財運運勢',
            ),
        );

        DB::table('constellation_lucky')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('constellation_lucky')->truncate();
    }
}
