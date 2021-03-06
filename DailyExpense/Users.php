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

    public function getImageInfo()
    {
        $q = 'select max(name) as imageName from sandbox.profileimages where user_id = '.$this->_user_id.'';
        global $pdo_dbh;
        $statement = $pdo_dbh->prepare($q);
        $statement->execute();
        return  $statement->fetch(PDO::FETCH_ASSOC);
    }

    public  function getDailySubTypes()
    {
        global $pdo_dbh;
        $q = 'select *, dy.name as sub_name, dy.id as id from sandbox.dailysubtypes dy
join sandbox.dailysupertypes dyy on dy.supertypeid = dyy.id
where (user_id is null or user_id='.$this->_user_id.')
order by dy.supertypeid, dy.name';
        $statement = $pdo_dbh->prepare($q);
        $statement->execute();
        $n = $statement->rowCount();
        if ($n > 0) {
            $res = array();
            for ($i = 0; $i < $n; $i++) {
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $res[] = $result;
            }
        }
        return $res;
    }
}

?>
