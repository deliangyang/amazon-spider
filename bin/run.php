<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/16/18
 * Time: 10:58 PM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$categories = require_once  __DIR__ . '/../config/categories.php';
echo 'start...', PHP_EOL;
foreach ($categories as $category) {
    try {
        $page = new \AmazonSpider\Amazon\RankPage(parsePages($category['url']), $category['name']);
        $page->execute();
        sleep(3);
    } catch (\Exception $ex) {
        echo $ex->getMessage(), PHP_EOL;
    }

    echo 'start next', PHP_EOL;
}

echo 'end...', PHP_EOL;

function parsePages($url)
{
    $urlPrefix = preg_replace('#ref=.+$#', '', $url);
    $url = $urlPrefix . 'ref=zg_bs_pg_%s?_encoding=UTF8&pg=%s';
    return $url;
}


