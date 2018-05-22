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

    protected $db;

    protected $config = array(
        'headers' => [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'en',
            ':method' => 'GET',
            ':authority' => 'www.amazon.com',
            'upgrade-insecure-requests' => 1,
            //':path' => '/Best-Sellers-Home-Kitchen/zgbs/home-garden/ref=zg_bs_pg_1?_encoding=UTF8&pg=1&ajax=1',
        ],
    );

    public function __construct()
    {
        global $db;
        $this->client = new Client($this->config);
        $this->db = $db;
    }

    abstract public function execute();

    public function getHtmlDom($url)
    {
        try {
            $option = $this->config;
            $path = str_replace('https://www.amazon.com', '', $url);
            $option[':path'] = $path;
            $req = $this->client->get($url, $option);
            $content = $req->getBody()->getContents();
            file_put_contents(__DIR__ . '/../../config/test.html', $content);
           # $content = file_get_contents(__DIR__ . '/../../config/test.html');
            $crawl = new Crawler($content);
            return $crawl;
        } catch (\Exception $ex) {

        }
        return false;
    }

    /**
     * 更新或者保存数据
     *
     * @param $data
     * @return bool|\mysqli_result
     */
    public function updateOrCreate($data)
    {
        $data += $this->defaultMeta;
        $md5_url = md5($data['url']);
        $data['md5_url'] = $md5_url;

        $tableName = 'amazon';
        $sql = <<<SQL
SELECT id FROM {$tableName} WHERE md5_url = '{$md5_url}' LIMIT 1
SQL;
        $query = $this->db->query($sql);
        $insert = [];

        foreach ($this->defaultMeta as $k => $val) {
            $insert[] = "{$k}='{$data[$k]}'";
        }
        if ($query->num_rows > 0) {
            $result = $query->fetch_assoc();
            $id = $result['id'];
            $insertSql['create_at'] = time();
            $insertSql = implode(',', $insert);
            $sql = <<<SQL
UPDATE {$tableName} SET {$insertSql} WHERE id={$id}
SQL;
        } else {
            $insertSql['update_at'] = time();
            $insertSql = implode(',', $insert);
            $sql = <<<SQL
INSERT INTO {$tableName} SET {$insertSql}
SQL;
        }

        return $this->db->query($sql);
    }

    public function saveExtra($url, $data)
    {
        $md5_url = md5($url);
        try {
            $tableName = 'amazon';
            $sql = <<<SQL
UPDATE {$tableName} SET asin='{$data['asin']}', date='{$data['date']}', category='{$data['category']}'
WHERE md5_url = '{$md5_url}';
SQL;
            $this->db->query($sql);
        } catch (\Exception $ex) {

        }
    }

    protected $defaultMeta = [
        'rank' => 0,
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

}
