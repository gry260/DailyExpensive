<?php
session_start();
require_once("DailyExpense/DailyExpense.php");
require_once("DailyExpense/Users.php");
require_once("DailyExpense/Comments.php");
require_once("misFunctions.php");
if (!empty($_SESSION['daily']['user_id'])) {
  $records = DailyExpense::generateObjects($_SESSION['daily']['user_id'], false);
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
<body class="skin-blue layout-boxed">
<div class="wrapper">

<header class="main-header">
  <!-- Logo -->
  <a href="index2.html" class="logo"><b>Admin</b>LTE</a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <?php
        if(empty($_SESSION['daily']['user_id'])) {
          ?>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="hidden-xs" class="">
              <button class="btn btn-block btn-warning btn-flat">Register</button>
            </span>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Body -->
              <li class="user-body">
                <form action="addUsers.php" method="POST" enctype="multipart/form-data">
                  <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" class="form-control" name="firstname" placeholder="Enter first name...">
                  </div>
                  <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" class="form-control" name="lastname" placeholder="Enter last name...">
                  </div>
                  <div class="form-group">
                    <label>Email:</label>
                    <input type="text" class="form-control" name="email" placeholder="Enter email...">
                  </div>
                  <div class="form-group">
                    <label>Password:</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter password...">
                  </div>
                  <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" class="form-control" name="confirm_password"
                           placeholder="Confirm password...">
                  </div>
                  <div class="form-group">
                    <label>Profile Image:</label>
                    <input type="file" name="profile_image"/>
                  </div>
                  <input type="hidden" name="temp_user_id" value="<?php echo $_SESSION['daily']['temp_user_id']; ?>"/>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <button type="submit" class="btn btn-primary">Register</button>
                </div>
              </li>
              </form>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><button class="btn btn-block btn-success">Sign In</button></span>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Body -->

              <li class="user-body">
                <form action="login.php" method="POST">
                  <div class="form-group">
                    <label>Email:</label>
                    <input type="text" class="form-control" name="email" placeholder="Enter Email...">
                  </div>
                  <div class="form-group">
                    <label>Password:</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter Password...">
                  </div>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <button class="btn btn-block btn-info">Login</button>
                </div>
              </li>
              </form>
            </ul>
          </li>
        <?php
        }
        else{
        ?>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="imageServe.php?file_path=<?php echo $file_path; ?>" class="img-circle" alt="User Image" style="width: 20px; height: 20px;">
              <span class="hidden-xs">    <?php echo $userInfo["lastname"].', '.$userInfo["firstname"]?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="imageServe.php?file_path=<?php echo $file_path; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $userInfo["lastname"].', '.$userInfo["firstname"]?>
                  <small>Member since Nov. 2012</small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="profile.php" class="btn btn-block btn-success">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="signout.php" class="btn btn-block btn-danger">Sign Out</a>
                </div>
              </li>
            </ul>
          </li>
        <?php
        }
        ?>
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
        <img src="imageServe.php?file_path=<?php echo $file_path; ?>" class="img-circle" alt="User Image"/>
      </div>
      <div class="pull-left info">
        <p><?php echo $userInfo["lastname"].', '.$userInfo["firstname"]?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      <li class="header">HEADER</li>
      <!-- Optionally, you can add icons to the links -->
      <li><a href="#"><span>Link</span></a><</li>
      <li><a href="profile.php"><span>Profile</span></a><</li>
      <li><a href="updates.php"><span>Update Existing Records</span></a><</li>
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
      Dashboard
      <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <!-- Main row -->
  <div class="col-md-6">
    <!-- Main content -->
    <section class="content" style="padding-left: 0px; padding-right: 0px;">

      <form method="post" action="addRecord.php">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">Add a Expense</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-xs-3">
                <input type="text" class="form-control" placeholder="Name" name="name">
              </div>
              <div class="col-xs-4">
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
              <div class="col-xs-4">
                <select class="form-control" id="sub_type" name="sub_type_id">
                  <?php
                  if (!empty($sub_types))
                    foreach ($sub_types as $key => $value)
                      echo '<option data="' . $value["supertypeid"] . '" value="' . $value["id"] . '">' . str_replace('_', ' ', $value["name"]) . '</option>';
                  ?>
                </select>
              </div>
            </div>
            <br/>

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
                <?php
                echo '<select name="payment_type_id" class="form-control">';
                if (!empty($payments)) {
                  foreach ($payments as $value) {
                    echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                  }
                }
                echo '</select>';
                ?>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-4">
                <br />
                <input type="text" name="url" class="form-control" id="url" placeholder="URL">
              </div>
              <div class="col-xs-4">
                <br />
                <input type="text" name="date" class="form-control" id="date" placeholder="Date of Expense">
              </div>
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
      <!-- Small boxes (Stat box) -->

      <form method="post" action="addType.php">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">Add a Sub-Category</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-xs-3">
                <input type="text" class="form-control" placeholder="Name" name="name">
              </div>
              <div>
                <div class="col-xs-3">
                  <select class="form-control" name="supertypeid">
                  <?php
                  if (!empty($general)) {
                    foreach ($general as $value) {
                      echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                    }
                  }
                  ?>
                  </select>
                </div>
                <div class="col-xs-3">
                  <input type="hidden" name="user_id" value="<?php echo $userInfo["id"]; ?>"/>
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- Left col -->
        <!-- Chat box -->
        <div class="box box-success">
          <div class="box-header">
            <i class="fa fa-comments-o"></i>

            <h3 class="box-title">Comments</h3>
          </div>
          <div class="box-body chat" id="chat-box">
            <!-- chat item -->
            <?php
            if(!empty($user_comments)){
              foreach($user_comments as $comment){
                echo '  <div class="item">
              <img src="imageServe.php?file_path='.$file_path.'" alt="user image" class="offline"/>
              <p class="message">
                <a href="#" class="name">
                  <small class="text-muted pull-right"><i class="fa fa-clock-o"></i>'.$comment->getDateTime().'</small>
                  '.$comment->getUserName().'
                </a>'.urldecode($comment->getComments()).'
              </p>
            </div>';
              }
            }
            ?>
            <!-- /.item -->
          </div>
          <!-- /.chat -->

          <form method="post" action="addComments.php">
            <?php
            if(!empty($_SESSION['daily']['user_id'])){
              echo '<input type="hidden" name="user_id" value="'.$_SESSION['daily']['user_id'].'"/>';
              echo '';
            }
            else if(!empty(  $_SESSION['daily']['temp_user_id'])){
              echo '<input type="hidden" name="temp_user_id" value="'. $_SESSION['daily']['temp_user_id'].'"/>';
            }
            ?>
            <div class="box-footer">
              <div class="input-group">
                <input class="form-control" placeholder="Type message..." name="comments"/>

                <div class="input-group-btn">
                  <button class="btn btn-success" type="submit" name="add_comments"><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- /.box (chat box) -->

  </div>
  <!-- /.Left col -->
  <div class="col-md-6">
    <section class="content" style="padding-left: 0px; padding-right: 0px;">
      <div class="box box-info">
        <div class="box-header">
          <h3 class="box-title">Spending Records</h3>
        </div>
        <div class="box-body">
          <div id="example1_wrapper" class="dataTables_wrapper form-inline" role="grid"><table id="example1" class="table table-bordered table-striped dataTable" aria-describedby="example1_info">
              <thead>
              <tr role="row"><th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 295px;">Name</th><th class="sorting" role="columnheader" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 423px;">Category</th><th class="sorting" role="columnheader" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 378px;">Sub-Category</th>
                <th class="sorting" role="columnheader" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 254px;">Date</th>
                <th class="sorting" role="columnheader" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 182px;">Amount</th>
                <th class="sorting" role="columnheader" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 182px;">Notes</th>
         </tr>
              </thead>
              <tbody role="alert" aria-live="polite" aria-relevant="all">
              <?php
              if(!empty($records)){
                foreach($records as $key => $value){
                  echo '<tr class="even">
                    <td>'.$value->getName().'</td>
                    <td>'.$value->getCategory().'</td>
                    <td>'.$value->getsubName().'</td>
                    <td>'.$value->getDate().'</td>
                    <td>'.$value->getAmount().'</td>
                    <td>'.$value->getNote().'</td>

                    </tr>';
                }
              }
              ?>
              </tbody></table></div>
        </div>
      </div>
    </section>
  </div>
</div>
<!-- /.row (main row) -->

</section><!-- /.content -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Version</b> 2.0
  </div>
  <strong>Copyright &copy; 2014-2015 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
  reserved.
</footer>
</div><!-- ./wrapper -->

<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="dist/js/app.min.js" type="text/javascript"></script>
<script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script src='plugins/fastclick/fastclick.min.js'></script>
<script src="dist/js/pages/dashboard.js" type="text/javascript"></script>
<script src="dist/js/daily.js" type="text/javascript"></script>
</body>
</html>