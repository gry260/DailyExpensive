<?php
session_start();
require_once("DailyExpense/DailyExpense.php");
if (!empty($_SESSION['daily']['user_id']))
  $records = DailyExpense::generateObjects($_SESSION['daily']['user_id']);
$general = DailyExpense::getDailySuperTypes();
$sub_types = DailyExpense::getDailySubTypes();
$payments = DailyExpense::getPayments();
?>
<!DOCTYPE HTML>
<html>
<head>
  <title>Telephasic by HTML5 UP</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <meta name="description" content=""/>
  <meta name="keywords" content=""/>
</head>
<body class="homepage">
<?php
if (!empty($records) && !empty($_SESSION['daily']['user_id'])) {
  foreach ($records as $key => $value) {
    echo '<form method="POST" action="addRecord.php">
        <input type="hidden" name="id" value="' . $value->getRecordID() . '" />
        <input type="hidden" name="user_id" value="' . $value->getUserID() . '" />
        <input type="text" name="notes" value="' . $value->getNote() . '" />
        <input type="url" name="url" value="' . $value->getUrl() . '" />
        <input type="date" name="date" value="' . $value->getDate() . '" />';
    echo '<select>';
    if (!empty($general)) {
      foreach ($general as $val) {
        echo '<option value="' . $val["id"] . '"';
        if ($val["id"] === $value->getSuperID()) {
          echo ' selected="selected" ';
        }
        echo '>' . $val["name"] . '</option>';
      }
    }
    echo '</select>';
    echo '<select name="sub_type_id">';
    if (!empty($sub_types)) {
      foreach ($sub_types as $val) {
        echo '<option';
        if ($val["id"] === $value->getSubTypeID()) {
          echo ' selected="selected" ';
        }
        echo ' value="' . $val["id"] . '">' . $val["name"] . '</option>';
      }
    }
    echo '</select>';
    echo '<select name="payment_type_id">';
    if (!empty($payments)) {
      foreach ($payments as $val) {
        echo '<option value="' . $val["id"] . '"';
        if ($val["id"] === $value->getPaymentID()) {
          echo ' selected="selected" ';
        }
        echo '>' . $val["name"] . '</option>';
      }
    }
    echo '</select>
    <input type="submit" value="update" />
        <input type="hidden" value="update" name="action_type" /></form>';
  }
}
?>
<form action="addRecord.php" method="POST">
  <?php
  echo '<select name="payment_type_id">';
  if (!empty($payments)) {
    foreach ($payments as $value) {
      echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
    }
  }
  echo '</select>';
  echo '<select>';
  if (!empty($general)) {
    foreach ($general as $value) {
      echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
    }
  }
  echo '</select>';
  ?>
  <?php
  echo '<select name="sub_type_id">';
  if (!empty($sub_types)) {
    foreach ($sub_types as $value) {
      echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
    }
  }
  echo '</select>';
  ?>
  <input type="text" name="notes" placeholder="notes"/>
  <input type="date" name="date"/>
  <input type="url" name="url" placeholder="url"/>
  <input type="hidden" name="user_id" value="<?php echo $_SESSION['daily']['user_id'] ?>"/>
  <input type="submit"/>
</form>
<br/>
<br/>

<form action="addUsers.php" method="POST" enctype="multipart/form-data">
  <input type="text" name="firstname" placeholder="firstname"/>
  <input type="text" name="lastname" placeholder="lastname"/>
  <input type="email" name="email" placeholder="email"/>
  <input type="password" name="password" placeholder="password">
  <input type="password" name="confirm_password" placeholder="confirm password"/>
  <input type="file" name="profile_image" placeholder="confirm password"/>
  <input type="submit"/>
</form>
<br/>

<form action="login.php" method="POST">
  <input type="email" name="email" placeholder="email"/>
  <input type="password" name="password" placeholder="password"/>
  <input type="submit"/>
</form>
</body>
</html>