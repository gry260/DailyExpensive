<?php

/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/2/8
 * Time: 19:58
 */
class Users
{
    private $_user_id;
    public function __construct($user_id)
    {
        $this->_user_id = $user_id;
    }


    public function getUserInfo()
    {
        $q = 'select * from sandbox.users where id = '.$this->_user_id.'';
        global $pdo_dbh;
        $statement = $pdo_dbh->prepare($q);
        $statement->execute();
        return  $statement->fetch(PDO::FETCH_ASSOC);
    }
}

?>
