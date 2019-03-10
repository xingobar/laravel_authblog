<?php
namespace App\Http\Services;

use App\Constellation;
use App\ConstellationDesc;
use App\ConstellationLucky;

class CrawlerService
{
    public function __constructor()
    {
        parent::constructor();
    }

    public function insertToDb($constellation_data, $today_date)
    {

        if (ConstellationDesc::where('date', '=', $today_date)->count() > 0) {
            echo 'data exists......';
            return;
        }

        $constellation_luckies = ConstellationLucky::all();
        $constellation_luckies_array = array();
        foreach ($constellation_luckies as $lucky) {
            $constellation_luckies_array[explode('運勢', $lucky['title'])[0]] = $lucky['id'];
        }

        $constellations = Constellation::all();
        $constellations_array = array();
        foreach ($constellations as $row) {
            $constellations_array[$row['name']] = $row['id'];
        }

        foreach ($constellation_data as $row) {
            $constellation_id = $constellations_array[$row['constellation']];

            foreach ($row['desc'] as $desc) {
                $lucky_id = $constellation_luckies_array[$desc['title']];

                ConstellationDesc::insert(
                    array(
                        'constellation_id' => $constellation_id,
                        'constellation_lucky_id' => $lucky_id,
                        'luck_star' => $desc['star'],
                        'date' => $today_date,
                        'description' => $desc['desc'],
                    )
                );
            }
        }

    }
}
