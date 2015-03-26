<?php
session_start();
require_once("DailyExpense/DailyExpense.php");
require_once("DailyExpense/Users.php");
require_once("DailyExpense/Comments.php");
require_once("misFunctions.php");
if (!empty($_SESSION['daily']['user_id'])) {
  $xml = httpPost($_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/daily/WebServices.php', array('user_id'=>$_SESSION['daily']['user_id'], "is_temp"=>"0"));
  $records = simplexml_load_string($xml);
  $user = new Users($_SESSION['daily']['user_id']);
  $sub_types = $user->getDailySubTypes();
  $userInfo = $user->getUserInfo();
  $userImages = $user->getImageInfo();
  $user_comments = Comments::getCommentsPerUser($_SESSION['daily']['user_id']);
  $file_path = 'files/profileimages/' . $_SESSION['daily']['user_id'] . '/' . $userImages['imageName'];
} else {
  require_once("DailyExpense/UsersTemp.php");
  $usertemp = new UsersTemp(md5(get_client_ip_server()));
  $usertemp->CheckUser();
  $bool = $usertemp->getIsInSystem();
  if ($bool == false) {
    require_once("db_abstract.php");
    $layer = new db_abstract_layer();
    $data = array("user_id" => '"' . $usertemp->getUserId() . '"');
    $_SESSION['daily']['temp_user_id'] = $layer->inserting($data, "users_temp");
  } else
    $_SESSION['daily']['temp_user_id'] = $usertemp->getID();

  $user_comments = Comments::getCommentsPerUser($_SESSION['daily']['temp_user_id']);
  $xml = httpPost($_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/daily/WebServices.php', array('user_id'=>$_SESSION['daily']['temp_user_id'], "is_temp"=>"1"));
  $records = simplexml_load_string($xml);
  $sub_types = DailyExpense::getDailySubTypes();
}
$general = DailyExpense::getDailySuperTypes();
$payments = DailyExpense::getPayments();
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="UTF-8">
  <title>AdminLTE 2 | Dashboard</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />

  <!-- Bootstrap 3.3.2 -->
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="dist/css/jquery.multiselect.css"  type="text/css" />
  <link rel="stylesheet" href="dist/css/jquery.multiselect.filter.css"  type="text/css" />
  <![endif]-->
</head>
<body class="skin-blue">
<div class="wrapper">
<!-- Main Header -->
<!-- Left side column. contains the logo and sidebar -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->


  <!-- Main content -->
  <section class="content">
    <div class="pad margin no-print">
      <div class="callout callout-info" style="margin-bottom: 0!important;">
        <h4><i class="fa fa-info"></i> Note:</h4>
        <div class="row">
          <div class="col-lg-2">
            <select name="example-optgroup" style="height:34px" id="cate_sub_types" multiple="multiple" size="5">
              <?php
              echo '<optgroup label="Bills">';
              if(!empty($sub_types)){
                foreach($sub_types as $sub_type){
                  if($sub_type["supertypeid"] != $lastID && !empty($lastID)){
                    echo '</optgroup><optgroup label="'.$sub_type["name"].'">';
                  }
                  $lastID = $sub_type["supertypeid"];
                  echo '<option value="'.$sub_type["id"].'">'.$sub_type["sub_name"].'</option>';
                }
              }
              echo '</optgroup>';
              ?>
            </select>
          </div>
          <div class="col-lg-2">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
              <input type="text" class="form-control" placeholder="Min Price" id="min_price">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
              <input type="text" class="form-control" placeholder="Max Price" id="max_price">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
              </div>
              <input type="text" class="form-control pull-right" id="reservation" class="reservation">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="input-group">
              <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
              </div>
              <?php
              echo '<select class="form-control" id="form_time_content">
                  <option value="today">Today</option>
                  <option value="yesterday">Yesterday</option>
                  <option value="last_7">Last 7 days</option>
                  <option value="last_30">Last 30 days</option>
                  <option value="this_month">This Month</option>
                  <option value="last_month">Last Month</option>
                  <option value="last_six_month">Last Six Months</option>
                </select>';
              ?>
            </div>
          </div>
          <div class="col-lg-2">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search"><span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

</div>

<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.3 -->
<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<!-- jQuery UI -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="dist/js/jquery.multiselect.js"></script>
<script src="dist/js/jquery.multiselect.filter.js"></script>
<script>
  $(document).ready(function () {
    $("#cate_sub_types").multiselect().multiselectfilter();
  });
</script>


<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience -->
</body>
</html>

<?php
echo strtotime("dec 15,2015");
?>



