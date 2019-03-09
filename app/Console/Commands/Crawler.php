<?php

namespace App\Console\Commands;

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

        foreach ($anchor_list as $row) {
            $page->goto($row['href']);
            $data = $page->evaluate(JsFunction::createWithBody("
                var selectedDate = document.querySelector('#iAcDay').value;
                var desc = document.querySelectorAll('.TODAY_CONTENT p:nth-child(2n + 1)');
                var star = document.querySelectorAll('.TODAY_CONTENT p:nth-child(2n)');
                var descList = [];

                for(var index = 0; index < desc.length; index++){
                    descList.push({
                        desc:desc[index].innerText,
                        star: star[index].innerText.split('運勢')[1]
                    });
                }

                return {selectedDate:selectedDate, desc:descList};
            "));

            print_r($data);
        }

        $browser->close();
    }
}
