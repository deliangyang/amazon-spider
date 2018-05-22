<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/17/18
 * Time: 9:37 PM
 */
require_once 'bootstrap.php';

#$crawl = new \AmazonSpider\Amazon\RankPage();
#$crawl->detailPage('https://www.amazon.com/AcuRite-Humidity-Thermometer-Hygrometer-Indicator/dp/B0013BKDO8/ref=zg_bs_home-garden_34/146-7563812-6215604?_encoding=UTF8&psc=1&refRID=A0NR62J0ENWXSJ4BMH9G');

$categories = require_once  __DIR__ . '/../config/categories.php';

foreach ($categories as $category) {
    $ranking = new \AmazonSpider\Amazon\EachRankingPage($category['url']);
    $ranking->execute();
    break;
}

exit;
$crawl = new \AmazonSpider\Amazon\Category();
$crawl->execute();
