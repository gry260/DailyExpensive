<?php
session_start();
require_once("DailyExpense/DailyExpense.php");
require_once("DailyExpense/Users.php");
require_once("misFunctions.php");
if (!empty($_SESSION['daily']['user_id'])) {
  $records = DailyExpense::generateObjects($_SESSION['daily']['user_id'], false);
  $user = new Users($_SESSION['daily']['user_id']);
  $sub_types = $user->getDailySubTypes();
  $userInfo = $user->getUserInfo();
  $userImages = $user->getImageInfo();
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
  $records = DailyExpense::generateObjects($_SESSION['daily']['temp_user_id'], true);
  $sub_types = DailyExpense::getDailySubTypes();
}

$general = DailyExpense::getDailySuperTypes();
$payments = DailyExpense::getPayments();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>AdminLTE 2 | Dashboard</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <!-- Bootstrap 3.3.2 -->
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <!-- Font Awesome Icons -->
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
        type="text/css"/>
  <!-- Ionicons -->
  <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css"/>
  <!-- Theme style -->
  <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link href="dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css"/>

  <link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css"/>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
  <![endif]-->
</head>
<body class="skin-blue">
<div class="wrapper">

<!-- Main Header -->
<header class="main-header">

  <!-- Logo -->
  <a href="index2.html" class="logo"><b>Admin</b>LTE</a>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Messages: style can be found in dropdown.less-->
        <li class="dropdown messages-menu">
          <!-- Menu toggle button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success">4</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have 4 messages</li>
            <li>
              <!-- inner menu: contains the messages -->
              <ul class="menu">
                <li><!-- start message -->
                  <a href="#">
                    <div class="pull-left">
                      <!-- User Image -->
                      <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
                    </div>
                    <!-- Message title and timestamp -->
                    <h4>
                      Support Team
                      <small><i class="fa fa-clock-o"></i> 5 mins</small>
                    </h4>
                    <!-- The message -->
                    <p>Why not buy a new awesome theme?</p>
                  </a>
                </li>
                <!-- end message -->
              </ul>
              <!-- /.menu -->
            </li>
            <li class="footer"><a href="#">See All Messages</a></li>
          </ul>
        </li>
        <!-- /.messages-menu -->

        <!-- Notifications Menu -->
        <li class="dropdown notifications-menu">
          <!-- Menu toggle button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell-o"></i>
            <span class="label label-warning">10</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have 10 notifications</li>
            <li>
              <!-- Inner Menu: contains the notifications -->
              <ul class="menu">
                <li><!-- start notification -->
                  <a href="#">
                    <i class="fa fa-users text-aqua"></i> 5 new members joined today
                  </a>
                </li>
                <!-- end notification -->
              </ul>
            </li>
            <li class="footer"><a href="#">View all</a></li>
          </ul>
        </li>
        <!-- Tasks Menu -->
        <li class="dropdown tasks-menu">
          <!-- Menu Toggle Button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-flag-o"></i>
            <span class="label label-danger">9</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have 9 tasks</li>
            <li>
              <!-- Inner menu: contains the tasks -->
              <ul class="menu">
                <li><!-- Task item -->
                  <a href="#">
                    <!-- Task title and progress text -->
                    <h3>
                      Design some buttons
                      <small class="pull-right">20%</small>
                    </h3>
                    <!-- The progress bar -->
                    <div class="progress xs">
                      <!-- Change the css width attribute to simulate progress -->
                      <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                           aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">20% Complete</span>
                      </div>
                    </div>
                  </a>
                </li>
                <!-- end task item -->
              </ul>
            </li>
            <li class="footer">
              <a href="#">View all tasks</a>
            </li>
          </ul>
        </li>
        <!-- User Account Menu -->
        <li class="dropdown user user-menu">
          <!-- Menu Toggle Button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <!-- The user image in the navbar-->
            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
            <!-- hidden-xs hides the username on small devices so only the image appears. -->
            <span class="hidden-xs">Alexander Pierce</span>
          </a>
          <ul class="dropdown-menu">
            <!-- The user image in the menu -->
            <li class="user-header">
              <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>

              <p>
                Alexander Pierce - Web Developer
                <small>Member since Nov. 2012</small>
              </p>
            </li>
            <!-- Menu Body -->
            <li class="user-body">
              <div class="col-xs-4 text-center">
                <a href="#">Followers</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="#">Sales</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="#">Friends</a>
              </div>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="#" class="btn btn-default btn-flat">Profile</a>
              </div>
              <div class="pull-right">
                <a href="#" class="btn btn-default btn-flat">Sign out</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
      </div>
      <div class="pull-left info">
        <p>Alexander Pierce</p>
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- search form (Optional) -->
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
      </div>
    </form>
    <!-- /.search form -->

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      <li class="header">HEADER</li>
      <!-- Optionally, you can add icons to the links -->
      <li><a href="#"><span>Link</span></a><</li>
      <li><a href="#"><span>Link</span></a><</li>
      <li><a href="#"><span>Link</span></a><</li>
      <li class="active"><a href="#"><span>Another Link</span></a></li>
      <li class="treeview">
        <a href="#"><span>Multilevel</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
          <li><a href="#">Link in level 2</a></li>
          <li><a href="#">Link in level 2</a></li>
        </ul>
      </li>
    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Page Header
      <small>Optional description</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
      <li class="active">Here</li>
    </ol>
  </section>

  <!-- Main content -->

  <div class="col-md-6">
    <section class="content">
      <form method="post" action="addRecord.php">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">Add a Expense</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-xs-3">
                <input type="text" class="form-control" placeholder="Name" name="Name">
              </div>
              <div class="col-xs-3">
                <select class="form-control" id="general">
                  <?php
                  if (!empty($general)) {
                    foreach ($general as $key => $value) {
                      echo '<option value="' . $value["id"] . '">' . str_replace('_', ' ', $value["name"]) . '</option>';
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-xs-3">
                <select class="form-control" id="sub_type" name="sub_type_id">
                  <?php
                  if (!empty($sub_types))
                    foreach ($sub_types as $key => $value)
                      echo '<option data="' . $value["supertypeid"] . '" value="' . $value["id"] . '">' . str_replace('_', ' ', $value["name"]) . '</option>';
                  ?>
                </select>
              </div>
              <div class="col-xs-3">
                <input type="text" name="date" class="form-control" id="date" placeholder="Date of Expense">
              </div>
            </div>
            <br/>

            <div class="row">
              <div class="col-xs-3">
                <input type="text" name="url" class="form-control" id="url" placeholder="URL">
              </div>
              <div class="col-xs-3">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                  <input type="text" class="form-control" name="amount">
                </div>
              </div>
              <div class="col-xs-3">
                <input type="text" class="form-control" placeholder="Notes" name="notes">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-3">
                <br/>
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <?php
        if(!empty($_SESSION['daily']['user_id']))
          echo '<input type="hidden" name="user_id" value="'.$_SESSION['daily']['user_id'].'"/>';
        else if(!empty(  $_SESSION['daily']['temp_user_id']))
          echo '<input type="hidden" name="temp_user_id" value="'. $_SESSION['daily']['temp_user_id'].'"/>';
        ?>
      </form>
    </section>
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<!-- Main Footer -->
<footer class="main-footer">
  <!-- To the right -->
  <div class="pull-right hidden-xs">

  </div>
  <!-- Default to the left -->
  <strong>Copyright &copy; 2015 <a href="#">Company</a>.</strong> All rights reserved.
</footer>

</div>
<!-- ./wrapper -->
<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="dist/js/app.min.js" type="text/javascript"></script>
<script src="dist/js/daily.js" type="text/javascript"></script>
<script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
</body>
</html>