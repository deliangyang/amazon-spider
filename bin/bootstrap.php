<?php
/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/21/18
 * Time: 9:47 PM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dbConfig = require_once __DIR__ . '/../config/database.php';
$db = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
$db->set_charset($dbConfig['charset']);
