<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/16/18
 * Time: 11:01 PM
 */

namespace AmazonSpider\Amazon;

use GuzzleHttp\Client;

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

}