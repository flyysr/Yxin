<?php
namespace Yxin;
require "db.php";
$dbconfig = require "config.php";

$db = new DB($dbconfig['host'], $dbconfig['port'], $dbconfig['db'], $dbconfig['username'], $dbconfig['password']);
$username = $db->query('select * from dbusers limit 1');
var_dump($username);
