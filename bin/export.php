<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/27/18
 * Time: 12:59 PM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dbConfig = require_once __DIR__ . '/../config/database.php';
$db = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
$db->set_charset($dbConfig['charset']);

$excel = new \PHPExcel();
$excel->setActiveSheetIndex(0);
$letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
$tableHeader = array('排名', '评论', 'prime', '价格', '名称', '图片', '链接', '评分', '目录');
$len = count($tableHeader);
for ($i = 0; $i < $len; $i++) {
    $excel->getActiveSheet()->setCellValue($letter[$i] . 1, $tableHeader[$i]);
}
$_count = 1;

foreach (queryData() as $key => $item) {
    $count = 0;
    $_count++;
    foreach ($item as $ii => $jj) {
        $excel->getActiveSheet()->setCellValue($letter[$count] . $_count, $jj);
        $count++;
    }
}


$write = new \PHPExcel_Writer_Excel5($excel);
$filename = __DIR__ . '/../doc/' . date('YmdHis') . '.xls';
$write->save($filename);

function queryData()
{
    global $db;
    $pageSize = 200;
    $page = 0;
    do {
        $offset = $page * $pageSize;
        $sql = <<<SQL
SELECT rank, review, prime, price, title, image, url, star, category FROM amazon ORDER BY category ASC, rank ASC LIMIT {$offset}, {$pageSize}
SQL;
        $query = $db->query($sql);
        if ($query->num_rows <= 0) {
            break;
        }
        $page++;
        while ($row = $query->fetch_assoc()) {
            yield $row;
        }

    } while (true);
}


