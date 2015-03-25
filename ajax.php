<?php
session_start();
require_once("misFunctions.php");

$data = array();
if(!empty($_POST['sub_type_id'])){
    if(!preg_match("/^[0-9]+$/", $_POST['sub_type_id'])){
        exit;
    }
    else {
        $sub_type_ids = array();
        $sub_type_ids[] = $_POST['sub_type_id'];
        $data["sub_type_ids"] = $_POST["sub_type_id"];
    }
}

if(!empty($_POST['min_price'])){
    if(!is_numeric($_POST['min_price'])){
        exit;
    }
    else
        $data["min_price"] = $_POST["min_price"];
}

if(!empty($_POST['max_price'])){
    if(!is_numeric($_POST['max_price'])){
        exit;
    }
    else
      $data["max_price"] = $_POST["max_price"];
}

if(!empty($_POST['start_date'])){
  $data["start_date"] = gmdate("Y-m-d",strtotime($_POST['start_date']));
}

if(!empty($_POST['end_date'])){
  $data["end_date"]= gmdate("Y-m-d",strtotime($_POST['end_date']));
}

if(!empty($_POST['last_type'])){
  switch($_POST['last_type']){
    case "today":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("today"));
      break;
    case "yesterday":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("yesterday"));
    break;
    case "last_7":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("-1 week"));
    break;
    case "last_30":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("-30 day"));
    break;
    case "this_month":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("first day of this month"));
    break;
    case "last_month":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("first day of last month"));
    break;
    case "last_six_month":
      $data["spec_date"]= gmdate("Y-m-d",strtotime("-6 months"));
    break;
  }
}

if (!empty($_SESSION['daily']['user_id'])) {
    $data['user_id']=$_SESSION['daily']['user_id'];     $data["is_temp"]="0";
    $xml = httpPost($_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/daily/WebServices.php', $data);
 //   echo $xml;
} else {
    $data['user_id']=$_SESSION['daily']['temp_user_id'];     $data["is_temp"]="1";
    $xml = httpPost($_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/daily/WebServices.php',$data);
    $records = simplexml_load_string($xml);
}

?>