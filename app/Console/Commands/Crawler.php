<?php

namespace App\Console\Commands;

use App\Http\Services\CrawlerService;
use Illuminate\Console\Command;
use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

class Crawler extends Command
{

    // 命令名稱
    protected $signature = 'crawler:start {--constellation=12}';
    protected $page = null;

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        $puppeteer = new Puppeteer;
        $browser = $puppeteer->launch(array('headless' => true, 'args' => array('--no-sandbox')));

        $this->page = $browser->newPage();
        $this->page->goto('http://astro.click108.com.tw/');

        $anchor_list = $this->crawlerConstellation();

        list($constellation_data, $today_date) = $this->crawlerConstellationDetail($anchor_list);

        $browser->close();

        $crawler_service = new CrawlerService();
        $crawler_service->insertToDb($constellation_data, $today_date);
        echo 'finish......';
    }

    /**
     * 抓取星座連結以及星座名稱
     *
     * @return array $anchor_list -> 包含星座連結以及名稱
     */
    public function crawlerConstellation()
    {
        $anchor_list = $this->page->evaluate(JsFunction::createWithBody("
            var anchor =  document.querySelectorAll('.STAR12_BOX li > a'), list = [];
            for(var index =0 ; index < " . $this->option('constellation') . "; index++) {
                list.push({
                        href: anchor[index].getAttribute('href'),
                        text: anchor[index].innerText
                    });
            }

            return list;
        "));

        return $anchor_list;
    }

    /**
     * 爬取星座詳細運勢資訊
     *
     * @param array $anchor_list 星座連結＆名稱資訊
     * @return mixed 星座詳細資訊、當天日期
     */
    public function crawlerConstellationDetail($anchor_list)
    {
        $constellation_data = array();
        $today_date = '';
        foreach ($anchor_list as $row) {
            $this->page->goto($row['href']);
            $data = $this->page->evaluate(JsFunction::createWithBody("
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

        return array($constellation_data, $today_date);
    }
}
