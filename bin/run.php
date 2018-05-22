<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/16/18
 * Time: 10:58 PM
 */
require_once __DIR__ . '/../vendor/autoload.php';

echo 'start...', PHP_EOL;
$page = new \AmazonSpider\Amazon\EachRankingPage();
$page->execute();
echo 'end...', PHP_EOL;
