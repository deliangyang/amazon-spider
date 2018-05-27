<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/16/18
 * Time: 11:00 PM
 */

namespace AmazonSpider\Amazon;

use Symfony\Component\DomCrawler\Crawler;

class RankPage extends BaseHttp
{

    protected $category;

    protected $currentTime;

    protected $url = 'https://www.amazon.com/Best-Sellers-Home-Kitchen/zgbs/home-garden/ref=zg_bs_pg_%s?_encoding=UTF8&pg=%s&ajax=1';

    public function __construct($url = '', $category = '', $currentTime = '')
    {
        parent::__construct();

        if ($url) {
            $this->url = $url;
        }
        $this->category = $category ?: date('Y-m-d_H:i:s');
        if (empty($currentTime)) {
            $this->currentTime = date('YmdHis');
        } else {
            $this->currentTime = $currentTime;
        }
    }

    public function getRanks()
    {
        for ($i = 1; $i <= 5; $i++) {
            try {
                $url = sprintf($this->url, $i, $i);
                echo $url, PHP_EOL;

                $req = $this->client->get($url);
                $content = $req->getBody()->getContents();

                $crawl = new Crawler();
                $crawl->addHtmlContent($content);
                $nodes = $crawl->filterXPath('//div[@class="zg_itemImmersion"]');
            } catch (\Exception $ex) {
                echo $ex->getMessage(), PHP_EOL;
                continue;
            }


            echo '[+] count:', $nodes->count(), PHP_EOL;
            if ($nodes->count() == 0) {
                $this->thePage($crawl);
                continue;
            }

            foreach ($nodes as $key => $node) {
                try {
                    $nextCrawl = new Crawler($node);
                    $rank = $nextCrawl->filterXPath('//span[@class="zg_rankNumber"]')->text();
                    try {
                        $title = $nextCrawl->filterXPath('//div[@class="p13n-sc-truncate p13n-sc-line-clamp-2"]')->text();
                    } catch (\Exception $ex) {
                        $title = $nextCrawl->filter('div.zg_itemWrapper div.p13n-sc-truncate')->text();
                    }
                    $star = $nextCrawl->filterXPath('//span[@class="a-icon-alt"]')->text();
                    $price = $nextCrawl->filterXPath('//span[@class="p13n-sc-price"]')->text();
                    $img = $nextCrawl->filterXPath('//img')->extract(['src']);
                    $url = $nextCrawl->filterXPath('//a[@class="a-link-normal"]')->extract(['href']);
                    $text = $nextCrawl->filterXPath('//a[@class="a-size-small a-link-normal"]')->text();
                    $urlDom = $nextCrawl->filter('a.a-size-small');
                    try {
                        $review = $urlDom->text();
                    } catch (\Exception $ex) {
                        $review = 0;
                    }
                    $data = [
                        'rank' => str_replace('.', '', trim($rank)),
                        'title' => trim($title),
                        'star' => $star,
                        'price' => $price,
                        'image' => $img[0],
                        'url' => 'https://www.amazon.com' . $url[0],
                        'text' => $text,
                        'prime' => '',
                        'category' => $this->category,
                        'review' => $review,
                    ];

                    if (preg_match('#Prime#i', $node->textContent)) {
                        $data['prime'] = 'Prime';
                    }
                    var_dump($data);
                    //echo 'url:', $data['url'], PHP_EOL;
                    yield $data;
                } catch (\Exception $ex) {
                    echo $ex->getMessage(), PHP_EOL;
                    file_put_contents(__DIR__ . '/../../cache/' . $this->category . '.html', $content);
                }
            }
        }
    }

    public function execute()
    {
        foreach ($this->getRanks() as $k => $item) {
            $this->updateOrCreate($item);
            sleep(1);
        }
    }

    protected function parseAttributes($content, &$item)
    {
        $data = explode("\n", $content);
        $values = [];
        foreach ($data as $jj) {
            $jj = str_replace([' ', "\n"], '', $jj);
            if (empty($jj)) {
                continue;
            }
            $values[] = $jj;
        }
        list($name, $value) = $values;
        if (preg_match('#ASIN#i', $name)) {
            $item['asin'] = $value;
        }
        if (preg_match('#Date First Available#i', $name)) {
            $item['date'] = $value;
        }
    }

    public function thePage(Crawler $crawl)
    {
        $eachItems = $crawl->filter('ol#zg-ordered-list li.zg-item-immersion');
        echo '[+] count:', $eachItems->count(), PHP_EOL;
        $allData = [];
        foreach ($eachItems as $node) {
            $node = new Crawler($node);
            try {
                $rank = $node->filter('span.zg-badge-text')->text();

                $title = $this->findTitle($node);
                $star = $node->filter('span.a-icon-alt')->text();
                $urlDom = $node->filter('a.a-size-small.a-link-normal');
                $url = $urlDom->attr('href');
                $review = $urlDom->text();
                $image = $node->filter('div.a-section.a-spacing-small>img')->attr('src');
                $price = $node->filter('span.p13n-sc-price')->text();
                $data = [
                    'rank' => str_replace(['.', '#'], '', trim($rank)),
                    'title' => trim($title),
                    'star' => $star,
                    'price' => $price,
                    'image' => $image,
                    'url' => 'https://www.amazon.com' . $url,
                    'review' => $review,
                    'category' => $this->category,
                ];
                var_dump($data);
                $this->updateOrCreate($data);
                $allData[] = $data;
                #return $data;
            } catch (\Exception $ex) {
                file_put_contents(__DIR__ . '/../../cache/' . $this->category . '.html', $crawl->html());
                var_dump([$ex->getMessage(), $ex->getLine(), $ex->getCode()]);
                throw $ex;
            }
        }
        return $allData;
    }

    protected function findTitle(Crawler $node)
    {
        $title = '';
        try {
            $title = $node->filter('div.p13n-sc-truncate')->text();
        } catch (\Exception $ex) {}
        try {
            $title = $node->filter('div.p13n-sc-truncate.p13n-sc-line-clamp-2')->text();
        } catch (\Exception $ex) {

        }
        try {
            $title = $node->filter('div.zg_itemWrapper div.p13n-sc-truncate')->text();
        } catch (\Exception $ex) {

        }
        try {
            $title = $node->filter('div.p13n-sc-truncate.p13n-sc-line-clamp-1')->text();
        } catch (\Exception $ex) {

        }
        return $title;
    }
}
