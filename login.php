<?php
/**
 * Created by PhpStorm.
 * User: gry260
 * Date: 2/5/15
 * Time: 12:59 PM
 *
 *
 *
 */
session_start();
require_once("emailAddressValidator.php");
require_once("passwordValidator.php");
$_SESSION['msgs']['login'] = array();
$data = array();

$email_validator = new EmailAddressValidator();
$password_validator = new Password();



if (!empty($_POST['email'])) {
  if (!$email_validator->check_email_address($_POST['email']))
    $_SESSION['msgs']['login'][] = "Your email address is not valid.";
  else
    $data["email"] = '"' . $_POST['email'] . '"';
} else {
  $_SESSION['msgs']['login'][] = 'Email is a required field.';
}


if (!empty($_POST['password'])) {
  $errors = $password_validator->validatePassword($_POST['password']);
  if (!empty($errors)) {
    foreach ($errors as $key => $msg)
      $_SESSION['msgs']['login'][] = $msg;
  } else
    $data["password"] = '"' . $_POST['password'] . '"';
}else
   $_SESSION['msgs']['login'][] = 'Password is a required field.';

require_once("db.php");
$db = new Database();
$pdh = $db->getConnection();
$statment = $pdh->prepare('select * from sandbox.users where email ='.$data["email"].' and password = '.$data["password"]);
$statment->execute();
$rowCount = $statment->rowCount();
if($rowCount > 0){
  $result = $statment->fetch(PDO::FETCH_ASSOC);
  $_SESSION['daily']['login'] = true;
  $_SESSION['daily']['user_id'] = $result["id"];
  header("Location:start.php");
  exit;
}
else
  $_SESSION['msgs']['login'] = 'The email and/or password you provided are incorrect';









?>