<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/30/18
 * Time: 8:33 PM
 */
$count = 0;
file_put_contents(__DIR__ . '/new_categories', '');
for ($i = 1; $i <= 5; $i++) {
    $content = file_get_contents(__DIR__ . '/' . $i);
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        $values = explode(',', $line);
        if (count($values) < 2) {
            continue;
        }
        list($name, $url) = $values;
        $name = trim($name, "\s\'");
        $url = trim($url);
        if (empty($name) || empty($url)) {
            continue;
        }
        if (false === strpos($url,'http')) {
            continue;
        }
        var_dump($name, $url);
        $count++;
        file_put_contents(__DIR__ . '/new_categories', "{$name}####{$url}" . PHP_EOL, FILE_APPEND);
    }
}

echo 'count:', $count, PHP_EOL;
