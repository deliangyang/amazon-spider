<?php
if (!isst($_GET['password']) || $_GET['password'] != '912ec803b2ce49e4a541068d495ab570') {
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<ul>
    <?php
    /**
     * Created by PhpStorm.
     * User: deliang
     * Date: 5/30/18
     * Time: 9:01 PM
     */

    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__), true);
    foreach ($dir as $k => $v) {
        $filename = $v->getFileName();
        if (false === strrpos($filename, '.xls')) {
            continue;
        }
        $date = date('Y-m-d H:i:s', intval(substr($filename, 0, 11)));
        echo <<<HTML
<li>
    <span>{$date}</span>
    <span>
    <a target="_blank" href="{$filename}">{$filename}</a>
</span>
</li>
HTML;

    }
    ?>
</ul>

</body>
</html>
