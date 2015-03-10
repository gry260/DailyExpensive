<?php
/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/3/8
 * Time: 12:36
 */


session_start();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
header("location: start.php");
exit;


?>