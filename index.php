<?php
session_start();
require_once("DailyExpense/DailyExpense.php");
require_once("DailyExpense/Users.php");
require_once("misFunctions.php");
if (!empty($_SESSION['daily']['user_id'])) {
  $records = DailyExpense::generateObjects($_SESSION['daily']['user_id'], false);
  $user = new Users($_SESSION['daily']['user_id']);
  $sub_types = $user ->getDailySubTypes();
  $userInfo = $user->getUserInfo();
  $userImages = $user->getImageInfo();
}
else {
  require_once("DailyExpense/UsersTemp.php");
  $usertemp = new UsersTemp(md5(get_client_ip_server()));
  $usertemp->CheckUser();
  $bool = $usertemp->getIsInSystem();
  if($bool == false){
    require_once("db_abstract.php");
    $layer = new db_abstract_layer();
    $data = array("user_id"=>'"'.$_SESSION['daily']['temp_user_id'].'"');
    $_SESSION['daily']['temp_user_id'] = $layer->inserting($data, "users_temp");
  }
  else
    $_SESSION['daily']['temp_user_id'] = $usertemp->getID();
    $records = DailyExpense::generateObjects($_SESSION['daily']['temp_user_id'], true);
    $sub_types = DailyExpense::getDailySubTypes();
}

$general = DailyExpense::getDailySuperTypes();
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
if (!empty($records) && (!empty($_SESSION['daily']['user_id']) || !empty($_SESSION['daily']['temp_user_id']))) {
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
  echo '</select>
  <input type="text" name="notes" placeholder="notes"/>
  <input type="date" name="date"/>
  <input type="url" name="url" placeholder="url"/>';
  if(!empty($_SESSION['daily']['user_id']))
    echo '<input type="hidden" name="user_id" value="'.$_SESSION['daily']['user_id'].'"/>';
  else if(!empty(  $_SESSION['daily']['temp_user_id'])){
    echo '<input type="hidden" name="temp_user_id" value="'. $_SESSION['daily']['temp_user_id'].'"/>';
  }
  echo '<input type="submit"/>
</form>
<br/>
<br/>';
if(empty($_SESSION['daily']['user_id']) && empty($userInfo)) {
  echo '<form action="addUsers.php" method="POST" enctype="multipart/form-data">
  <input type="text" name="firstname" placeholder="firstname"/>
  <input type="text" name="lastname" placeholder="lastname"/>
  <input type="email" name="email" placeholder="email"/>
  <input type="password" name="password" placeholder="password">
  <input type="password" name="confirm_password" placeholder="confirm password"/>
  <input type="file" name="profile_image" placeholder="confirm password"/>
  <input type="hidden" name="temp_user_id" value="'.$_SESSION['daily']['temp_user_id'].'"/>
  <input type="submit" name="add_user"/>
</form>
<br/><form action="login.php" method="POST">
  <input type="email" name="email" placeholder="email"/>
  <input type="password" name="password" placeholder="password"/>
  <input type="submit"/>
</form>';
}
if(!empty($_SESSION['daily']['user_id']) && !empty($userInfo)) {
  echo '<br /><br /><form action="addUsers.php" method="POST" enctype="multipart/form-data">
    <label>First Name:</label><input type="text" name="firstname" placeholder="firstname" value="';
  echo $userInfo['firstname'];
  echo '">';
  echo '<label>Last Name:</label><input type="text" name="lastname" placeholder="lastname" value="' . $userInfo['lastname'] . '">';
  echo '<label>Password:</label><input type="password" name="password" placeholder="password">';
  echo '<label>Confirm Password</label> <input type="password" name="confirm_password" placeholder="confirm password"/>';
  $file_path = urlencode('files/profileimages/' . $_SESSION['daily']['user_id'] . '/' . $userImages['imageName']);
  echo '<img src="imageServe.php?file_path=' . $file_path . '" width="100" height="100" />';
  echo '<input type="file"  name="profile_image"/>';
  echo '<input type="hidden" name="user_id" value="' . $_SESSION['daily']['user_id'] . '"/>
<input type="submit" name="update_user"/>
</form>';
}
  if (!empty($records) && (!empty($_SESSION['daily']['user_id']))) {
    ?>
    <br/>
    <br/>
    <form method="post" action="addType.php">
      <?php
      echo '<select name="supertypeid">';
      if (!empty($general)) {
        foreach ($general as $value) {
          echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
        }
      }
      echo '</select>';
      ?>
      <input type="text" name="name"/>

      <input type="hidden" name="user_id" value="<?php echo $userInfo["id"]; ?>"/>
      <input type="submit"/>
    </form>
  <?php
  }
  if (!empty($_SESSION['daily']['user_id']) || !empty($_SESSION['daily']['temp_user_id'])) {
    ?>
  <form method="post" action="getReports.php">
    <?php
    if(!empty($general)){
      echo '<br /><br /><br />';
      foreach($general as $key => $value){
        echo '<input type="checkbox" name="general_type" />'.$value["name"].'&nbsp;&nbsp;&nbsp;&nbsp;';
      }
    }
    echo '<br /><br />';
    echo  '<select>
        <option>Last 24 Hours</option>
        <option>Last Week</option>
        <option selected>Last Month</option>
        <option>Last 6 Months</option>
        <option>Last Year</option>
        <option>Last 5 Year</option>
        <option>Last 10 Year</option>
        </select>';
    ?>
    <br /><br />Reports In
    <input type="checkbox" name="report[]" value="category"/>Category Report &nbsp;  &nbsp;
    <input type="checkbox" name="report[]" value="subcategory"/>SubCategory Report &nbsp;  &nbsp;
    <input type="checkbox" name="report[]" value="monthly" />Monthly Report &nbsp;  &nbsp;
    <input type="checkbox" name="report[]" value="annual"/>Annual Report &nbsp;  &nbsp;
    <br /><br /><input type="submit" />
  </form>
<?php
}
?>
</body>
</html>