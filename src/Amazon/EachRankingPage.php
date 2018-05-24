<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/17/18
 * Time: 10:26 PM
 */
namespace AmazonSpider\Amazon;

use Symfony\Component\DomCrawler\Crawler;

class EachRankingPage extends BaseHttp
{

    protected $ref = 'ref=zg_bs_pg_%s?_encoding=UTF8&pg=%s';

    protected $totalPage = 2;

    protected $categoryUrl;

    public function __construct($categoryUrl)
    {
        parent::__construct();

        $this->categoryUrl = $categoryUrl;
    }

    public function execute()
    {
        foreach ($this->parsePages() as $k => $pageUrl) {
            $items = $this->getPageItems($pageUrl);
            foreach ($items as $key => $item) {
                $item = $this->detailPage($item);
                var_dump($item);
            }
        }
    }

    public function getPageItems($url)
    {
        $crawl = $this->getHtmlDom($url);
        $allData = [];
        if ($crawl) {
            echo '[+] ', $url, PHP_EOL;
            $crawlNodes = $crawl->filter('div.zg_itemImmersion');
            echo '[+] count:', $crawlNodes->count(), PHP_EOL;

            if ($crawlNodes->count() == 0) {
                return $this->thePage($crawl);
            }

            foreach ($crawlNodes as $crawl) {
                $node = new Crawler($crawl);
                $rank = $node->filter('span.zg_rankNumber')->text();
                $title = $node->filter('div.zg_itemWrapper div.p13n-sc-truncate')->text();
                $star = $node->filter('span.a-icon-alt')->text();
                $urlDom = $node->filter('a.a-size-small');
                try {
                    $url = $urlDom->attr('href');
                    $review = $urlDom->text();
                } catch (\Exception $ex) {
                    $urlDom = $node->filter('a.a-link-normal')->first();
                    $url = $urlDom->attr('href');
                    $review = 0;
                }
//                $url = $urlDom->attr('href');
//                $review = $urlDom->text();
                $image = $node->filter('div.a-section.a-spacing-mini>img')->attr('src');
                $price = $node->filter('span.p13n-sc-price')->text();
                $data = [
                    'rank' => str_replace(['.', '#'], '', trim($rank)),
                    'title' => trim($title),
                    'star' => $star,
                    'price' => $price,
                    'image' => $image,
                    'url' => 'https://www.amazon.com' . $url,
                    'review' => $review,
                ];
                $this->updateOrCreate($data);
                $allData[] = $data;
            }

        }
        return $allData;
    }

    protected function parsePages()
    {
        $urlPrefix = preg_replace('#ref=.+$#', '', $this->categoryUrl);
        for ($i = 1; $i <= 2; $i++) {
            yield $urlPrefix . sprintf($this->ref, $i, $i);
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

    protected $defaultMeta = [
        'rank' => '',
        'title' => '',
        'star' => '',
        'price' => '',
        'image' => '',
        'url' => '',
        'review' => '',
        'category' => '',
        'asin' => '',
        'date' => '',
        'md5_url' => '',
    ];

    /**
     * 获取详情页的信息
     *
     * @param array $item
     * @return array
     */
    public function detailPage(array $item = [])
    {
        $crawl = $this->getHtmlDom($item['url']);
        if ($crawl) {
            try {
                $category = $crawl->filterXPath('//div[@id="wayfinding-breadcrumbs_feature_div"]')->text();
                $category = (str_replace([' ', "\n"], '', $category));
                $item['category'] = str_replace('›', ' › ', $category);

                $attributes = $crawl->filterXPath('//table[@id="productDetails_detailBullets_sections1"]/tr');
                foreach ($attributes as $attribute) {
                    $this->parseAttributes($attribute->textContent, $item);
                }

                $data = $item + $this->defaultMeta;
                var_dump($data);
                $this->saveExtra($data);
                return $data;
            } catch (\Exception $exception) {

            }
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
                $title = $node->filter('div.p13n-sc-truncate.p13n-sc-line-clamp-2')->text();
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
                ];
                $this->updateOrCreate($data);
                var_dump($data);
                $allData[] = $data;
                #return $data;
            } catch (\Exception $ex) {
                var_dump([$ex->getMessage(), $ex->getLine(), $ex->getCode()]);
            }
        }

        return $allData;
    }
}
