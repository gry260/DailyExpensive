<?php
require_once("DailyExpense/DailyExpense.php");
$records = DailyExpense::generateObjects(1);
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
if (!empty($records)) {
  foreach ($records as $key => $value) {
    echo '<form>
        <input type="hidden" name="id" value="' . $value->getRecordID() . '" />
        <input type="hidden" name="user_id" value="' . $value->getUserID() . '" />
        <input type="text" name="notes" value="' . $value->getNote() . '" />
        <input type="text" name="url" value="' . $value->getUrl() . '" />
        <input type="text" name="date" value="' . $value->getDate() . '" />';
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
    echo '<select>';
    if (!empty($sub_types)) {
      foreach ($sub_types as $val) {
        echo '<option';
        if ($val["id"] === $value->getSubTypeID()) {
          echo ' selected="selected" ';
        }
        echo ' value="'.$val["id"] . '">'.$val["name"].'</option>';
      }
    }
    echo '</select>';
    echo '<select>';
    if (!empty($payments)) {
      foreach ($payments as $val) {
        echo '<option value="' . $val["id"] . '"';
        if ($val["id"] === $value->getPaymentID()) {
          echo ' selected="selected" ';
        }
        echo '>' . $val["name"] . '</option>';
      }
    }
    echo '</select>';

    echo '</form>';
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
  <input type="text" name="notes"/>
  <input type="date" name="date"/>
  <input type="url" name="url"/>
  <input type="hidden" name="user_id" value="1"/>
  <input type="submit"/>
</form>
<br />
<br />
<form action="">
  <input type="text" name="firstname"/>
  <input type="text" name="lastname"/>
  <input type="email" name="email" />
  <input type="password" name="password">
  <input type="submit" />
</form>
</body>
</html>