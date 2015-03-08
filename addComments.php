<?php
session_start();
$_SESSION['msgs']['record'] = array();
$data = array();
$_SESSION['msgs'] = array();
if(!empty($_POST['temp_user_id'])){
  if(!preg_match('/^[0-9]+$/', $_POST['temp_user_id']))
    $_SESSION['msgs'][] = "The User Id is invalid. Please contact IT.";
  else{
    $data["is_temp"] = "'1'";
    $data["user_id"] = trim($_POST['temp_user_id']);
  }
}
else if(!empty($_POST['user_id'])) {
  if(!preg_match('/^[0-9]+$/', $_POST['user_id']))
    $_SESSION['msgs'][] = "The User Id is invalid. Please contact IT.";
  else
    $data["user_id"] = trim($_POST['user_id']);
}
else
  $_SESSION['msgs'][] = "The User Id is invalid. Please contact IT.";


if(!empty($_POST['comments']))
  $data["comments"] = '"'.urlencode($_POST['comments']).'"';
else
  $_SESSION['msgs'][] = "Please provide a comments";

$data["date_time"] = '"'.date('Y-m-d H:i:s').'"';


require_once("db_abstract.php");
$layer = new db_abstract_layer();

if(!empty($data)){
  $lastId = $layer->inserting($data, "comments");
}

header("location: start.php");
exit;










?>