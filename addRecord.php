<?php
session_start();
$_SESSION['msgs']['record'] = array();
$data = array();

require_once("db_abstract.php");
if (!empty($_POST['sub_type_id'])) {
  if (!preg_match('/^[0-9]+$/', $_POST['sub_type_id'])) {
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
  } else
    $data["sub_type_id"] = $_POST['sub_type_id'];

} else {
  $_SESSION['msgs']['record'][] = "Please Contact IT.";
}


if (!empty($_POST['payment_type_id'])) {
  if (!preg_match('/^[0-9]+$/', $_POST['payment_type_id'])) {
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
  } else {
    $data["payment_id"] = $_POST['payment_type_id'];
  }
} else {
  $_SESSION['msgs']['record'][] = "Please Contact IT.";
}


if (!empty($_POST['user_id'])) {
  if (!preg_match('/^[0-9]+$/', $_POST['user_id'])) {
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
  } else {
    $data["user_id"] = $_POST['user_id'];
  }
}
else if(!empty($_POST['temp_user_id'])){
  if (!preg_match('/^[0-9]+$/', $_POST['temp_user_id'])) {
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
  } else {
    $data["user_id"] = $_POST['temp_user_id'];
    $data["is_temp"] = '"1"';
  }
}
else {
  $_SESSION['msgs']['record'][] = "Please Contact IT.";
}


if (!empty($_POST['id'])) {
  $where = array();
  if (!preg_match('/^[0-9]+$/', $_POST['id'])) {
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
  } else {
    $where["id"] = $_POST['id'];
  }
} else {
  $_SESSION['msgs']['record'][] = "Please Contact IT.";
}


if (!empty($_POST['name'])) {
  $data["name"] = '"'.htmlentities($_POST['name'], ENT_QUOTES).'"';
}


if (!empty($_POST['notes'])) {
  $data["notes"] = '"'.htmlentities($_POST['notes'], ENT_QUOTES).'"';
}

if (!empty($_POST['amount'])) {
  if(!is_numeric($_POST['amount'])){
    $_SESSION['msgs'] =  'The amount you have entered is not numeric.';
  }
  else
    $data["amount"] = '"'.htmlentities($_POST['amount'], ENT_QUOTES).'"';
}



if (!empty($_POST['date'])) {
  if (!preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $_POST["date"])) {
    $_SESSION['msgs']['record'][] = "Your URL is incorrect. Please enter the correct format of date in yyyy-mm-dd.";
  } else {
      $piece = explode("/", $_POST['date']);
      $data["date"] = '"'.$piece[2].'-'.$piece[0].'-'.$piece[1].'"';
    }
}

if (!empty($_POST["url"])) {
  if (!filter_var($_POST["url"], FILTER_VALIDATE_URL)) {
    $_SESSION['msgs']['record'][] = "Your URL is incorrect. Please enter correct format of URL";
  } else
      $data["url"] = '"'.$_POST["url"].'"';
}


$layer = new db_abstract_layer();
if($_POST['action_type'] === "update"){
  $layer->updating($data, "daily_record", $where);
}
else{
  $layer->inserting($data, "daily_record");
}
header("location: start.php");
exit;









?>