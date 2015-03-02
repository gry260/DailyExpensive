<?php

/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/2/8
 * Time: 19:58
 */
class UsersTemp
{
    private $_id;
    private $_user_id;
    private $_is_in_system;
    public function __construct($user_id)
    {
        $this->_user_id = $user_id;
    }

    public function getID()
    {
        return $this->_id;
    }

    public function setUserId($user_id)
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

    public function CheckUser()
    {
        $q = 'select * from sandbox.users_temp where user_id = "'.$this->_user_id.'"';
        global $pdo_dbh;
        $statement = $pdo_dbh->prepare($q);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(empty($result)) {
            $this->isInSystem(false);
            return false;
        }
        else {
            $this->_id = $result["id"];
            $this->isInSystem(true);
            return $result;
        }
    }

    public function isInSystem($bool)
    {
        $this->_is_in_system = $bool;
    }

    public function getIsInSystem()
    {
        return $this->_is_in_system;
    }

    public static function GenerateTempUser($user_id)
    {

    }



}

?>
