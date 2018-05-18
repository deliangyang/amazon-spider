<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/16/18
 * Time: 11:01 PM
 */

namespace AmazonSpider\Amazon;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseHttp
{

    protected $client;

    protected $config = array(
        'headers' => [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'en',
            ':method' => 'GET',
            ':authority' => 'www.amazon.com',
            'upgrade-insecure-requests' => 1,
            ':path' => '/Best-Sellers-Home-Kitchen/zgbs/home-garden/ref=zg_bs_pg_1?_encoding=UTF8&pg=1&ajax=1',
        ],
    );

    public function __construct()
    {
        $this->client = new Client($this->config);
    }

    abstract public function execute();

    public function getHtmlDom($url)
    {
        try {
            $req = $this->client->get($url);
            $content = $req->getBody()->getContents();
            file_put_contents(__DIR__ . '/../../config/test.html', $content);
            $crawl = new Crawler($content);
            return $crawl;
        } catch (\Exception $ex) {

        }
        return false;
    }

}
