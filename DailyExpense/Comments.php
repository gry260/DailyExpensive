<?php

class Comments
{
  private $_user_name;
  private $_user_id;
  private $_comments;
  private $_date_time;
  private $_istemp;

  public function __construct($data)
  {
    if(is_array($data)) {
      if(!empty($data["user_id"])) $this->_user_id = $data["user_id"];
      if(!empty($data["date_time"])) $this->_date_time = $data["date_time"];
      if(!empty($data["comments"])) $this->_comments = $data["comments"];
      if(!empty($data["is_temp"])) $this->_istemp = $data["is_temp"];
      if(!empty($data["username"])) $this->_user_name = $data["username"];
    }
  }

  public static function generateObjects($user_id)
  {
    return new Comments($user_id);
  }

  public function getUserName()
  {
    return $this->_user_name;
  }

  public function getUserId()
  {
    return $this->_user_id;
  }

  public function getDateTime()
  {
    return $this->_date_time;
  }

  public function getTemp()
  {
    return $this->_istemp;
  }

  public function getComments()
  {
    return $this->_comments;
  }

  public static function getCommentsPerUser($user_id)
  {
    global $pdo_dbh;
    $q = ' select *, concat(u.lastname, ", ", u.firstname) as username,
    DATE_FORMAT(date_time,"%b %d %Y %h:%i %p") as date_time
    from sandbox.comments c left join sandbox.users u on u.id = c.user_id where user_id = ' . $user_id.'
     order by date_time desc';

    $statement = $pdo_dbh->prepare($q);
    $statement->execute();
    $n = $statement->rowCount();
    if ($n > 0) {
      $res = array();
      for ($i = 0; $i < $n; $i++) {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $res[] = new Comments($result);
      }
      return $res;
    }
    else
      return false;
  }

}

?>
