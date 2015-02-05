<?php
require_once("init.php");

abstract class DailyExpense
{
  protected $_user_id;
  protected $_notes;
  protected $_type_id;
  protected $_super_type_id;
  protected $_sub_type_id;
  protected $_url;
  protected $_id;
  protected $_date;
  protected $_payment_id;

  public static function generateObjects($user_id)
  {
    global $pdo_dbh;
    $q = 'select *, dy.name as sub_name, dyy.name as super_name, d.id as id from sandbox.daily_record d left join sandbox.dailysubtypes dy on dy.id = d.sub_type_id
    left join dailysupertypes dyy on dy.supertypeid = dyy.id
    where d.user_id = ' . $user_id . '';

    $statement = $pdo_dbh->prepare($q);
    $statement->execute();
    $n = $statement->rowCount();
    if ($n > 0) {
      $res = array();
      for ($i = 0; $i < $n; $i++) {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (file_exists('DailyExpense/DailyCategory/' . $result["super_name"] . '/' . $result["sub_name"] . '.php')) {
          require_once('DailyExpense/DailyCategory/' . $result["super_name"] . '.php');
          require_once('DailyExpense/DailyCategory/' . $result["super_name"] . '/' . $result["sub_name"] . '.php');
          $record = new $result["sub_name"]();
          $record->setUserID($user_id);
          if (!empty($result["notes"]))
            $record->setNote($result["notes"]);
          if (!empty($result["url"]))
            $record->setUrl($result["url"]);
          if (!empty($result["sub_type_id"]))
            $record->setSubTypeID($result["sub_type_id"]);
          if (!empty($result["id"]))
            $record->setRecordID($result["id"]);
          if (!empty($result["date"]))
            $record->setDate($result["date"]);
          if (!empty($result["payment_id"]))
            $record->setPaymentID($result["payment_id"]);
          if(!empty($result["supertypeid"])){
            $record->setSuperID($result["supertypeid"]);
          }

          $res[$result["id"]] = $record;
        }
      }
      return $res;
    }
  }

  public static function getDailySuperTypes()
  {
    global $pdo_dbh;
    $q = 'select * from sandbox.dailysupertypes';
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

  public static function getDailySubTypes()
  {
    global $pdo_dbh;
    $q = 'select * from sandbox.dailysubtypes';
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

  public static function getPayments()
  {
    global $pdo_dbh;
    $q = 'select * from sandbox.payments_types';
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

  public function setPaymentID($payment_id)
  {
    $this->_payment_id = $payment_id;
  }

  public function getPaymentID()
  {
    return $this->_payment_id;
  }

  public function setSuperID($super_id)
  {
    $this->_super_type_id = $super_id;
  }

  public function getSuperID()
  {
    return $this->_super_type_id;
  }

  public function setUserID($userid)
  {
    $this->_user_id = $userid;
  }

  public function getUserID()
  {
    return $this->_user_id;
  }


  public function setRecordID($id)
  {
    $this->_id = $id;
  }
  public function getRecordID()
  {
    if(!empty($this->_id)){
      return $this->_id;
    }
    else
      return false;

  }

  public function getUrl()
  {
    if (!empty($this->_url))
      return $this->_url;
    else
      return false;
  }

  public function setUrl($the_url)
  {
    $this->_url = $the_url;
  }

  public function setNote($the_n)
  {
    $this->_notes = $the_n;
  }

  public function getNote()
  {
    if (!empty($this->_notes))
      return $this->_notes;
    else
      return false;
  }

  public function getDate()
  {
    return $this->_date;
  }

  public function setDate($the_d)
  {
    $this->_date = $the_d;
  }

  public function setSubTypeID($the_sub_is)
  {
    $this->_sub_type_id = $the_sub_is;
  }

  public function getSubTypeID()
  {
    if (!empty($this->_sub_type_id))
      return $this->_sub_type_id;
    else
      return false;
  }
}

?>