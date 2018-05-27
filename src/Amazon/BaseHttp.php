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

    protected $db;

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
        global $db;
        $this->db = $db;
    }

    abstract public function execute();

    /**
     * 更新或者保存数据
     *
     * @param $data
     * @return bool|\mysqli_result
     */
    public function updateOrCreate($data)
    {
        $data += $this->defaultMeta;
        $rank = $data['rank'];
        $category = $data['category'];
        $tableName = 'amazon';
        $sql = <<<SQL
SELECT id FROM {$tableName} WHERE rank = $rank AND category='{$category}' LIMIT 1
SQL;
        $query = $this->db->query($sql);
        $insert = [];
        foreach ($this->defaultMeta as $k => $val) {
            $insert[] = "{$k}='{$data[$k]}'";
        }
        if ($query->num_rows > 0) {
            $result = $query->fetch_assoc();
            $id = $result['id'];
            $insertSql = implode(',', $insert);
            $sql = <<<SQL
UPDATE {$tableName} SET {$insertSql} WHERE id={$id}
SQL;
        } else {
            $insertSql = implode(',', $insert);
            $sql = <<<SQL
INSERT INTO {$tableName} SET {$insertSql}
SQL;
        }
        var_dump($sql);
        return $this->db->query($sql);
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
        'date' => '',
        'prime' => '',
    ];



}
