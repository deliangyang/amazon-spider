<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/16/18
 * Time: 10:58 PM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dbConfig = require_once __DIR__ . '/../config/database.php';
$db = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
$db->set_charset($dbConfig['charset']);

$categories = require_once  __DIR__ . '/../config/categories.php';
$time = date('YmdHis');
echo 'start...', PHP_EOL;
foreach ($categories as $category) {
    try {
        $page = new \AmazonSpider\Amazon\RankPage(parsePages($category['url']), $category['name'], $time);
        $page->execute();
        sleep(3);
    } catch (\Exception $ex) {
        echo $ex->getMessage(), PHP_EOL;
    }

    echo 'start next', PHP_EOL;
}

echo 'end...', PHP_EOL;

echo 'do export...', PHP_EOL;

require_once 'run.php';

function parsePages($url)
{
    $urlPrefix = preg_replace('#ref=.+$#', '', $url);
    $url = $urlPrefix . 'ref=zg_bs_pg_%s?_encoding=UTF8&pg=%s&ajax=1';
    return $url;
}


