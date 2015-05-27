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

if(!empty($_SESSION['daily']['search_records'])){
  unset($_SESSION['daily']['search_records']);
}


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
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->

    <link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
    <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- Bootstrap 3.3.2 -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    <link rel="stylesheet" href="dist/css/jquery.multiselect.css"  type="text/css" />
    <link rel="stylesheet" href="dist/css/jquery.multiselect.filter.css"  type="text/css" />
    <link href="dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="dist/css/style.css" rel="stylesheet" type="text/css"/>
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
        <li class="active"><a href="#"><span>Link</span></a><</li>
        <li><a href="#"><span>Another Link</span></a></li>
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
        Update Your Profile
      </h1>
    </section>

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
                  <option value="">Please Select an Option</option>
                  <option value="yesterday">Last 24 hours</option>
                  <option value="last_7">Last 7 days</option>
                  <option value="last_30">Last 30 days</option>
                  <option value="this_month">This Month</option>
                  <option value="last_six_month">Last Six Months</option>
                </select>';
                ?>
              </div>
            </div>
            <div class="col-lg-2">
              <div class="input-group">
                <input type="text" class="form-control"  id="search_records" placeholder="Search"><span class="input-group-btn">
                <button type="submit" name="search" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <ul class="timeline">
        <?php
        if(!empty($records)){
          echo "<input type='hidden' id='user_sub_types' value='".json_encode($sub_types)."'/>";
          foreach($records as $date => $value){
            $tdate= ltrim ($date, 'M');
            $the_date = gmdate("M d, Y", $tdate);
            echo '<li class="time-label" value="'.$date.'">
                <span class="bg-green">'.$the_date.'</span>
                </li>
                <li>
                  <i class="fa fa-user bg-aqua"></i>
                   <div class="timeline-item">
                   <h3 class="timeline-header"><a href="#.">Support Team</a> ..</h3>';
            echo '<div class="row" style="padding:15px;" id="each_row_'.$date.'">';
            foreach($value as $kk => $vv){
              $encode = array();
              $encode["sub_type_id"] = (int)$vv->subtypeid[0];
              $encode["amount"] = (string)$vv->amount[0];
              echo '
                    <div id="'.$vv->id.'" class="col-lg-1" style="background-color: #f5f5f5;
  border: 1px solid #e3e3e3;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
  box-shadow: inset 0 1px 1px rgba(0,0,0,.05); margin-left:15px; padding-right:175px;
  padding-top:15px; padding-bottom:15px;
  "><a class="remove_record" id="remove_record_'.(int) $vv->id[0].'" style="cursor:pointer;"><i class="fa fa-remove"></i> </a>
                     <div class="timeline-body">
                     <div class="product-info">
                        </a>
                        <span class="info-box-number record_amount" style="font-size:14px;">$'.$vv->amount[0].'</span>
                        </div>
                       <div class="product-img">
                        <img src="http://placehold.it/50x50/d2d6de"  height="100" width="150" alt="Product Image">
                      </div>
                      <div class="product-info">';
              if(!empty($vv->name[0])){
                $encode["name"] =(string) $vv->name[0];
                echo '<h4 class="box-title" style="margin-top:2px; margin-bottom:5px;">
                <a class="record_url" href="'.$vv->url.'" target="_blank">'.$vv->name[0].'</a></h4>';
              }
              if(!empty($vv->note)){
                $encode["note"] = (string)$vv->note;
                echo '<span class="product-description record_note">
                          '.$vv->note.'
                        </span><br />';
              }
              if(!empty($vv->url[0])){
                $encode["url"] =(string) $vv->url;
              }
              $encode["id"] =(int) $vv->id[0];
              $encode["date"] = gmdate("m/d/Y",$tdate+3600);
              $encode["super_type_id"] = (int)$vv->superid[0];
              $encode["payment_type_id"] =(int) $vv->paymentid[0];
              echo "<input type='hidden' value='".json_encode($encode)."' name='each_record' id='each_record' />";
              echo '<a class="btn btn-primary btn-xs slide_open btn btn-danger btn-sm edit_record" id="edit_record">Edit</a>
                      </div>
                     </div>
                     </div>';
            }
            echo '</div></div>
            </li>';
          }
        }
        ?>
      </ul>
    </section>
  </div>
  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2015 <a href="#">Company</a>.</strong> All rights reserved.
  </footer>

  </div>

  <div id="slide" class="well" style="width: 600px;">
    <?php
      if (!empty($sub_types)) {
        echo "<input type='hidden' value='".json_encode($sub_types)."' id='sub_types'/>";
      }
    ?>
    <h4>Update Daily Expense Record</h4>
    <form method="post" action="addRecord.php">
      <?php
      echo '<input type="hidden" name="id" value="">';
      ?>
      <div class="box box-info">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-3">
              <input type="text" class="form-control" placeholder="Name" name="name">
            </div>
            <div class="col-xs-4">
              <select class="form-control" id="general">
                <option value="1">Bills</option><option value="2">Education</option><option value="3">Food</option><option value="4">Personal</option><option value="5">Transportation</option>                </select>
            </div>
            <div class="col-xs-4">
              <select class="form-control" id="sub_type_id" name="sub_type_id">
              </select>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col-xs-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" class="form-control" name="amount">
              </div>
            </div>
            <div class="col-xs-3">
              <input type="text" class="form-control" placeholder="Notes" name="notes">
            </div>
            <div class="col-xs-5">
              <select name="payment_type_id" class="form-control"><option value="1">Credit Card</option><option value="2">Debit Card</option><option value="3">Cash</option></select>              </div>
          </div>
          <div class="row">
            <div class="col-xs-4">
              <br>
              <input type="text" name="url" class="form-control" id="url" placeholder="URL">
            </div>
            <div class="col-xs-4">
              <br>
              <input type="text" name="date" class="form-control" id="date" placeholder="Date of Expense">
            </div>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <input type="hidden" value="update" name="action_type">
      <button class="btn btn-default" type="submit" value="update" >Update</button>
      <button class="slide_close btn btn-default">Close</button>
    </form>

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 2.1.3 -->
  <script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>

  <!-- jQuery UI -->
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>

  <!-- Bootstrap 3.3.2 JS -->
  <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/app.min.js" type="text/javascript"></script>

  <script src="dist/js/jquery.popupoverlay.js"></script>

  <script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
  <script src="dist/js/jquery.multiselect.js"></script>
  <script src="dist/js/jquery.multiselect.filter.js"></script>

  <script src="dist/js/daily.js"></script>

  <script>
    $(document).ready(function () {
      $('#slide').popup({
        focusdelay: 400,
        outline: true,
      vertical: 'top'
    });
      $("#cate_sub_types").multiselect().multiselectfilter();
      $(".edit_record").editRecord();
      $('#date').datepicker({});
      $('input[name="multiselect_cate_sub_types"]').Switch();
      $('#min_price, #max_price').Price();
      $('#general').Select({"name": "#sub_type_id"});
      $('#reservation').daterangepicker();
      $('.daterangepicker .btn-success').reservation();
      $('#form_time_content').lastType();
      $("#search_records").searchRecords();
      $('.remove_record').removeRecord();
    });
  </script>
  </body>
  </html>




<div id="example_record" class="col-lg-1" style="background-color: #f5f5f5;
  border: 1px solid #e3e3e3;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
  box-shadow: inset 0 1px 1px rgba(0,0,0,.05); margin-left:15px; padding-right:175px;
  padding-top:15px; padding-bottom:15px;
  "><a class="remove_record" id="remove_record_79" style="cursor:pointer;"><i class="fa fa-remove"></i> </a>
  <div class="timeline-body">
    <div class="product-info">
      <span class="info-box-number record_amount" style="font-size:14px;">$10.00</span>
    </div>
    <div class="product-img">
      <img src="http://placehold.it/50x50/d2d6de" height="100" width="150" alt="Product Image">
    </div>
    <div class="product-info"><h4 class="box-title" style="margin-top:2px; margin-bottom:5px;">
        <a class="record_url" href="" target="_blank">test</a></h4><span class="product-description record_note">
                          test
                        </span><br><input type="hidden" value="{&quot;sub_type_id&quot;:1,&quot;amount&quot;:&quot;10.00&quot;,&quot;name&quot;:&quot;test&quot;,&quot;note&quot;:&quot;test&quot;,&quot;id&quot;:79,&quot;date&quot;:&quot;05\/24\/2015&quot;,&quot;super_type_id&quot;:1,&quot;payment_type_id&quot;:0}" name="each_record" id="each_record"><a class="btn btn-primary btn-xs slide_open btn btn-danger btn-sm edit_record" id="edit_record" data-popup-ordinal="0">Edit</a>
    </div>
  </div>
</div>