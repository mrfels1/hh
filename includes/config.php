<?php

$dbUser = 'root';
$dbPassword = '';
$dbName = 'hhdb';

$db = new PDO('mysql:host=127.0.0.1;dbname=' . $dbName . '; charset=utf8', $dbUser, $dbPassword);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

define('APP_NAME', 'Test_task_hh');
