<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/27/18
 * Time: 4:59 PM
 */
require_once __DIR__ . '/../vendor/autoload.php';

#$url = 'https://www.amazon.com/Best-Sellers-Appstore-Android/zgbs/mobile-apps/ref=zg_bs_pg_1?_encoding=UTF8&pg=1&ajax=1';
#$url = 'https://www.amazon.com/best-sellers-books-Amazon/zgbs/books/ref=zg_bs_pg_1?_encoding=UTF8&pg=1&ajax=1';
$url = 'https://www.amazon.com/Best-Sellers-Magazines/zgbs/magazines/ref=zg_bs_pg_1?_encoding=UTF8&pg=1&ajax=1';
$page = new \AmazonSpider\Amazon\RankPage($url, 'test', time());
$page->execute();
