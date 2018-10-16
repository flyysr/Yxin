<?php
namespace Yxin;
include "db.php";
$dbConfig = include "config.php";

$db = new DB($dbConfig['host'], $dbConfig['port'], $dbConfig['db'], $dbConfig['username'], $dbConfig['password']);
$user = $db->query('select * from dbusers limit 1;');
var_dump($user);

