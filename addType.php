<?php
/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/2/16
 * Time: 10:59
 */
session_start();
require_once("misFunctions.php");
require_once("db_abstract.php");

$layer = new db_abstract_layer();
$_SESSION['msgs']['subtype'] = array();
$data = array();

if (!empty($_POST["name"])) {
    $allowedChars = array('_');
    $chkSpChar = valUserData($_POST['name'], $allowedChars);
    if ($chkSpChar !== true) {
        $_SESSION['msgs']["user"][] = 'Your sub type contains invalid characters.
		    Please remove the following characters: ' . trim($chkSpChar);
    } else {
        $file = ucfirst($_POST["name"]).'.php';
        $class = $_POST["name"];
        $data["name"] = '"' . ucfirst($_POST['name']) . '"';
    }

} else
    $_SESSION['msgs']['user'][] = 'Sub type is a required field.';


if (!empty($_POST['supertypeid'])) {
    if (!preg_match('/^[0-9]+$/', $_POST['supertypeid'])) {
        $_SESSION['msgs']['user'][] = "Please Contact IT.";
    } else {
        $lastId = htmlentities($_POST['supertypeid'], ENT_QUOTES);
        $data['supertypeid'] = $lastId;
    }
} else {
    $_SESSION['msgs']['user'][] = "Please Contact IT.";
}

if (!empty($_POST['user_id'])) {
    if (!preg_match('/^[0-9]+$/', $_POST['user_id'])) {
        $_SESSION['msgs']['user'][] = "Please Contact IT.";
    } else
        $data["user_id"] = htmlentities($_POST['user_id'], ENT_QUOTES);
} else {
    $_SESSION['msgs']['user'][] = "Please Contact IT.";
}

require_once("init.php");
$q = 'select * from sandbox.dailysupertypes where id = ' . $data["supertypeid"] . '';
$statement = $pdo_dbh->prepare($q);
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$supername = $result["name"];
$lastId = $layer->inserting($data, "dailysubtypes");
if (!empty($lastId)) {
    if (is_dir("UsersDailyExpense")) {
        if (!is_dir("UsersDailyExpense/" . $data["user_id"]))
            mkdir("UsersDailyExpense/" . $data["user_id"], 0777, true);
        if (!is_dir("UsersDailyExpense/" . $data["user_id"] . '/' . $supername))
            mkdir("UsersDailyExpense/" . $data["user_id"] . '/' . $supername, 0777, true);
        $handle = fopen("./UsersDailyExpense/" . $data["user_id"] . '/' . $supername.'/'.$file, 'w+') or die('Cannot open file:  ' . $file);
        $text = '<?php
        class '.$class.' extends '.$supername.'
        {

        }
        ?>
        ';
        fwrite($handle, $text);
        fclose($handle);
    }
}
header("Location:index.php");
exit;

?>