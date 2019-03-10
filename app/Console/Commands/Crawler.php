<?php

namespace App\Console\Commands;

use App\Constellation;
use App\ConstellationDesc;
use App\ConstellationLucky;
use Illuminate\Console\Command;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

class Crawler extends Command
{

    // 命令名稱
    protected $signature = 'test:Log';

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        $puppeteer = new Puppeteer;
        $browser = $puppeteer->launch(array('headless' => true, 'args' => array('--no-sandbox')));

        $page = $browser->newPage();
        $page->goto('http://astro.click108.com.tw/');

        $anchor_list = $page->evaluate(JsFunction::createWithBody("
            var anchor =  document.querySelectorAll('.STAR12_BOX li > a'), list = [];
            for(var index =0 ; index < anchor.length; index++) {
                list.push({
                        href: anchor[index].getAttribute('href'),
                        text: anchor[index].innerText
                    });
            }

            return list;
        "));

        $constellation_data = array();
        $today_date = '';
        foreach ($anchor_list as $row) {
            $page->goto($row['href']);
            $data = $page->evaluate(JsFunction::createWithBody("
                var selectedDate = document.querySelector('#iAcDay').value;
                var desc = document.querySelectorAll('.TODAY_CONTENT p:nth-child(2n + 1)');
                var star = document.querySelectorAll('.TODAY_CONTENT p:nth-child(2n)');
                var descList = [];

                for(var index = 0; index < desc.length; index++){
                    var splittedText = star[index].innerText.split('運勢');
                    descList.push({
                        desc:desc[index].innerText,
                        star: splittedText[1],
                        title: splittedText[0]
                    });
                }

                return {selectedDate:selectedDate, desc:descList};
            "));

            $constellation_data[] = array(
                'constellation' => $row['text'],
                'desc' => $data['desc'],
            );
            $today_date = $data['selectedDate'];
        }

        $browser->close();

        if (ConstellationDesc::where('date', '=', $today_date)->count() > 0) {
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
