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

    protected $url = 'https://www.amazon.com/Best-Sellers-Home-Kitchen/zgbs/home-garden/ref=zg_bs_pg_%s?_encoding=UTF8&pg=%s&ajax=1';

    public function getRanks()
    {
        for ($i = 1; $i <= 5; $i++) {
            $url = sprintf($this->url, $i, $i);
            echo $url, PHP_EOL;
            $req = $this->client->get($url);
            $content = $req->getBody()->getContents();

            $crawl = new Crawler();
            $crawl->addHtmlContent($content);
            $nodes = $crawl->filterXPath('//div[@class="zg_itemImmersion"]');
            foreach ($nodes as $key => $node) {
                try {
                    $nextCrawl = new Crawler($node);
                    $rank = $nextCrawl->filterXPath('//span[@class="zg_rankNumber"]')->text();
                    $title = $nextCrawl->filterXPath('//div[@class="p13n-sc-truncate p13n-sc-line-clamp-2"]')->text();
                    $star = $nextCrawl->filterXPath('//span[@class="a-icon-alt"]')->text();
                    $price = $nextCrawl->filterXPath('//span[@class="p13n-sc-price"]')->text();
                    $img = $nextCrawl->filterXPath('//img')->extract(['src']);
                    $url = $nextCrawl->filterXPath('//a[@class="a-link-normal"]')->extract(['href']);
                    $text = $nextCrawl->filterXPath('//a[@class="a-size-small a-link-normal"]')->text();
                    $data = [
                        'rank' => str_replace('.', '', trim($rank)),
                        'title' => trim($title),
                        'star' => $star,
                        'price' => $price,
                        'image' => $img[0],
                        'url' => 'https://www.amazon.com' . $url[0],
                        'text' => $text,
                    ];
                    echo 'url:', $data['url'], PHP_EOL;
                    yield $data;
                } catch (\Exception $ex) {
                    echo $ex->getMessage(), PHP_EOL;
                }
            }
        }
    }

    public function execute()
    {
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex(0);
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $tableHeader = array('排名', '名称', '评分', '价格', '图片', '链接', '评论', '分类', 'ASIN', 'Date');
        $len = count($tableHeader);
        for ($i = 0; $i < $len; $i++) {
            $excel->getActiveSheet()->setCellValue($letter[$i] . 1, $tableHeader[$i]);
        }
        $_count = 1;
        foreach ($this->getRanks() as $k => $item) {
            try {
                $req = $this->client->get($item['url']);
                $content = $req->getBody()->getContents();
                $crawl = new Crawler();
                $crawl->addHtmlContent($content);

                $category = $crawl->filterXPath('//div[@id="wayfinding-breadcrumbs_feature_div"]')->text();
                $category = (str_replace([' ', "\n"], '', $category));
                $item['category'] = str_replace('›', ' › ', $category);

                $attributes = $crawl->filterXPath('//table[@id="productDetails_detailBullets_sections1"]/tr');
                foreach ($attributes as $attribute) {
                    $this->parseAttributes($attribute->textContent, $item);
                }
            } catch (\Exception $ex) {
                echo $ex->getMessage(), PHP_EOL;
            }
            $item += $this->defaultMeta;
            var_dump($item);
            $count = 0;
            $_count++;
            foreach ($item as $ii => $jj) {
                $excel->getActiveSheet()->setCellValue($letter[$count] . $_count, $jj);
                $count++;
            }
            sleep(2);
        }

        $write = new \PHPExcel_Writer_Excel5($excel);
        $write->save(__DIR__ . '/../../doc/' . date('Y-m-d_H:i:s') . '.xls');

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

    protected $defaultMeta = [
        'asin' => '',
        'date' => '',
        'category' => '',
    ];
}
