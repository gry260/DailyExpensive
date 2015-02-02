<?php
session_start();
$_SESSION['msgs']['record'] = array();
$data = array();

require_once("db.php");

if(!empty($_POST['sub_type_id'])){
    if(!preg_match('/^[0-9]+$/',$_POST['sub_type_id'])){
        $_SESSION['msgs']['record'][] = "Please Contact IT.";
    }
    else
        $data["sub_type_id"]=$_POST['sub_type_id'];

}
else{
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
}


if(!empty($_POST['payment_type_id'])){
    if(!preg_match('/^[0-9]+$/',$_POST['payment_type_id'])){
        $_SESSION['msgs']['record'][] = "Please Contact IT.";
    }
    else{
        $data["payment_type_id"] = $_POST['payment_type_id'];
    }
}
else{
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
}


if(!empty($_POST['notes'])){
   $data["notes"] =  htmlentities($_POST['notes'], ENT_QUOTES);
}

if(!empty($_POST['date'])){
    if(!preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $_POST["date"])){
        $_SESSION['msgs']['record'][] = "Please Contact IT.";
    }
    else
        $data["date"]=$_POST["date"];
}





?>