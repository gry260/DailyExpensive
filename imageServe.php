<?php
/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/2/11
 * Time: 19:40
 */

if(!empty($_GET['file_path'])) {
    $file_path = $_GET['file_path'];
    $file_path = urldecode($file_path);
    $parts = pathinfo($file_path);
    if(file_exists($file_path)) {
        $contents = file_get_contents($file_path);
        switch ($parts["extension"]) {
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            default:
        }
        header('Content-type: ' . $ctype);
        echo $contents;
        exit;
    }
    else{
        $contents = file_get_contents("dist/img/avatar5.png");
        header('Content-type: png');
        echo $contents;
        exit;
    }
}




?>