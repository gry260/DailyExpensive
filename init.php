<?php
/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/2/1
 * Time: 17:20
 */

date_default_timezone_set('America/Los_Angeles');
require_once("db.php");
$db = new Database();
$pdo_dbh = $db->getConnection();
?>
