<?php
/**
 * Created by PhpStorm.
 * User: gry260
 * Date: 2/4/15
 * Time: 12:59 PM
 */
session_start();
require_once("misFunctions.php");
require_once("db_abstract.php");
$layer = new db_abstract_layer();

$_SESSION['msgs']['user'] = array();
$data = array();
if (!empty($_POST["firstname"])) {
  $allowedChars = array(',');
  $chkSpChar = valUserData($_POST['firstname'], $allowedChars);
  if ($chkSpChar !== true) {
    $_SESSION['msgs']["user"][] = 'Your first name contains invalid characters.
		    Please remove the following characters: ' . trim($chkSpChar);
  } else
    $data["firstname"] = '"' . $_POST['firstname'] . '"';

} else
  $_SESSION['msgs']['user'][] = 'First name is a required field.';

if (!empty($_POST["lastname"])) {
  $allowedChars = array(',');
  $chkSpChar = valUserData($_POST['lastname'], $allowedChars);
  if ($chkSpChar !== true) {
    $_SESSION['msgs']["user"][] = 'Your last name contains invalid characters.
		    Please remove the following characters: ' . trim($chkSpChar);
  } else
    $data["lastname"] = '"' . $_POST['lastname'] . '"';

} else
  $_SESSION['msgs']['user'][] = 'Last name is a required field.';

require_once("emailAddressValidator.php");
require_once("passwordValidator.php");

if (!empty($_POST['email'])) {
  $email_validator = new EmailAddressValidator();
  if (!$email_validator->check_email_address($_POST['email']))
    $_SESSION['msgs']['user'][] = "Your email address is not valid.";
  else
    $data["email"] = '"' . $_POST['email'] . '"';
} else {
  $_SESSION['msgs']['user'][] = 'Email is a required field.';
}

$password_validator = new Password();
if (!empty($_POST['password'])) {
  $errors = $password_validator->validatePassword($_POST['password']);
  if (!empty($errors)) {
    foreach ($errors as $key => $msg)
      $_SESSION['msgs']['user'][] = $msg;
  } else
    $data["password"] = '"' . $_POST['password'] . '"';
} else
  $_SESSION['msgs']['user'][] = 'Password is a required field.';

if (!empty($_POST['confirm_password'])) {
  $errors = $password_validator->validatePassword($_POST['confirm_password']);
  if (!empty($errors)) {
    foreach ($errors as $key => $msg)
      $_SESSION['msgs']['user'][] = $msg;
  }
} else
  $_SESSION['msgs']['user'][] = 'Confirm Password is a required field.';

if (trim($_POST['password']) !== trim($_POST['confirm_password'])) {
  $_SESSION['msgs']['user'][] = "Password does not match with confirmed password. Please enter it again.";
}

if (!empty($_FILES['profile_image']) && is_array($_FILES['profile_image'])) {
  $file_info = array();
  $file_info['file_info'] = $_FILES['profile_image'];
  $file_info['max_file_size'] = '10485760';
  $file_info['allowed_extensions'] = array('png', 'jpg');
  $msgs = ValidateFile($file_info);
  if ($msgs !== true)
    $_SESSION['msgs']['user'] = $msgs;
}

require_once("db.php");
$db = new Database();
$pdh = $db->getConnection();
$statment = $pdh->prepare('select * from sandbox.users where email ='.$data["email"]);
$statment->execute();
$rowCount = $statment->rowCount();
if($rowCount >0){
  $_SESSION['msgs']['user'][] = 'This email has been taken. Please user another email address.';
  header("location: index.php");
  exit;
}

if(!empty($_POST['update_user'])){
  if (!empty($_POST['user_id'])) {
    if (!preg_match('/^[0-9]+$/', $_POST['user_id'])) {
      $_SESSION['msgs']['record'][] = "Please Contact IT.";
    } else {
      $where = array("id"=>$_POST['user_id']);
    }
  } else {
    $_SESSION['msgs']['record'][] = "Please Contact IT.";
  }
  $layer->updating($data, "users", $where);
}
else if (!empty($data)) {
  $lastId = $layer->inserting($data, "users");
}
if (!empty($_FILES['profile_image']) && is_array($_FILES['profile_image'])) {
  $ext = pathinfo($file_info['file_info']['name'], PATHINFO_EXTENSION);
  $current_date = time();
  $received_date = date('Y-m-d H:i:s');
  $timestamp = date('YmdHis', $current_date);
  $new_filename = "$timestamp.$ext";
  if (!file_exists('files/profileimages/' . $lastId)) {
    mkdir('files/profileimages/' . $lastId, 0777, true);
  }
  $file_path = 'files/profileimages/' . $lastId . '/' . $new_filename;
  $file_upload = array('destination_file_path' => $file_path, 'seed' => NULL, 'tmp_name' => $file_info['file_info']['tmp_name']);
  $msgs = UploadFile($file_upload);
  if ($msgs !== true)
    $_SESSION['msgs']['user'] = $msgs;

  $data = array("user_id" => $lastId, "name" => '"' . $new_filename . '"');
  $lastId = $layer->inserting($data, "profileimages");
}

header("Location: index.php");
exit;

?>