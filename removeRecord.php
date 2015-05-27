<?php
/**
 * Created by PhpStorm.
 * User: gry260
 * Date: 5/21/15
 * Time: 3:16 PM
 */


if(!empty($_POST["id"])){
  $record_id = $_POST["id"];
}
require_once("DailyExpense/DailyExpense.php");
DailyExpense::removeRecord($record_id);
?>