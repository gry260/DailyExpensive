<?php
/**
 * Created by PhpStorm.
 * User: gry260
 * Date: 2/26/15
 * Time: 12:06 PM
 */



session_start();
$require = array("category", "subcategory", "monthly", "annual");
if(!empty($_SESSION['daily']['user_id']))  {
  $user_id = $_SESSION['daily']['user_id'];
}
else{
  echo 'You are not authorized to use this program.';
  exit;
}
require_once('db.php');
$db = new Database();
$connection = $db->getConnection();

if(!empty($_POST['report']) && is_array($_POST['report'])){
  $_SESSION['daily']['reports'] = array();
  foreach($_POST['report'] as $value){
    if(!in_array($value, $require)){
      echo 'Invalid category options. Please exit the programs';
      exit;
    }
    $q = 'select sum(amount), dsuper.name, dsub.name as subname,
    DATE_FORMAT(d.date, "%M") as monthly,  DATE_FORMAT(d.date, "%Y") as yearly
     from sandbox.daily_record d
    join sandbox.dailysubtypes dsub on d.sub_type_id = dsub.id
    join sandbox.dailysupertypes dsuper on dsuper.id = dsub.supertypeid
    where d.user_id ='.$user_id;
    switch($value)  {
      case "category":
        $q .= ' group by dsub.supertypeid ';
      break;
      case "subcategory":
        $q .= ' group by dsub.id ';
      break;
      case "monthly":
        $q .= ' group by DATE_FORMAT(d.date, "%M") ';
      break;
      case "annual":
        $q .= ' group by DATE_FORMAT(d.date, "%Y") ';
      break;
    }
    $statement = $connection->prepare($q);
    $statement->execute();
    $n = $statement->rowCount();
    if($n > 0){
      for($i=0; $i<$n; $i++){
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $_SESSION['daily']['reports'][$value][] = $result;
      }
    }
  }
  header("Location: index.php");
  exit;
}
?>