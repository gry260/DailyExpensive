<?php
require_once("init.php");

abstract class DailyExpense
{
  protected $_amount;
  protected $_user_id;
  protected $_notes;
  protected $_type_id;
  protected $_super_type_id;
  protected $_sub_type_id;
  protected $_url;
  protected $_id;
  protected $_date;
  protected $_payment_id;
  protected $_name;
  protected $_category;
  protected $_sub_name;
  protected $_payment_name;

  public static function generateObjects($user_id, $isTemp, $where)
  {
    global $pdo_dbh;
    $q = 'select  dy.user_id as uid, dy.name as sub_name, dyy.name as super_name, d.id as id, concat("M",UNIX_TIMESTAMP(d.date)) as date,
    d.url, d.notes, d.amount, d.sub_type_id, dy.supertypeid, d.name as record_name, d.url as url
    from sandbox.daily_record d
    left join sandbox.dailysubtypes dy on dy.id = d.sub_type_id
    left join dailysupertypes dyy on dy.supertypeid = dyy.id
    where d.user_id = ' . $user_id . ' and (dy.user_id is null or dy.user_id = ' . $user_id . ')';

    if(!empty($where["min_price"])){
      $q .= ' and d.amount >= '.$where['min_price'].' ';
    }

    if(!empty($where["max_price"])){
      $q .= ' and d.amount <= '.$where['max_price'].' ';
    }

    if(!empty($where["spec_date"])){
      $today = date("Y-m-d");
      $q .= ' and (d.date >= "'.$where['spec_date'].'"  and d.date <= "'.$today.'")';
    }

    if(!empty($where["start_date"])){
      $q .= ' and d.date >= "'.$where['start_date'].'" ';
    }

    if(!empty($where["end_date"])){
      $q .= ' and d.date <= "'.$where['end_date'].'" ';
    }

    if(!empty($where["text"])){
      $q .= ' and (d.amount like  "%'.$where['text'].'%" or d.name like  "%'.$where['text'].'%"
      or d.notes like  "%'.$where['text'].'%" or d.url like  "%'.$where['text'].'%") ';
    }

    if ($isTemp == true)
      $q .= ' and d.is_temp = "1"';
    if(!empty($where) && array_key_exists("sub_type_ids", $where)){
      $q .= ' and (';
      foreach($where["sub_type_ids"] as $sub_type_id){
        $q .= '  d.sub_type_id = '.$sub_type_id .' or ';
      }
      $q = substr($q, 0, -3).')';
    }

    $q .= '
    union
select dy.user_id as uid, dy.name as sub_name, dyy.name as super_name, d.id as id, concat("M",UNIX_TIMESTAMP(d.date)) as date,
d.url, d.notes, d.amount, d.sub_type_id, dy.supertypeid, d.name as record_name, d.url as url
from sandbox.users u
left join sandbox.users_temp temp on u.temp_user_id = temp.id
left join sandbox.daily_record d on d.user_id = temp.id
left join sandbox.dailysubtypes dy on dy.id = d.sub_type_id
left join dailysupertypes dyy on dy.supertypeid = dyy.id
where u.';

    if ($isTemp == true)
      $q .= 'temp_user_id=';
    else
      $q .= 'id=';
    $q .= $user_id . ' and d.is_temp = "1"';

    if(!empty($where["spec_date"])){
      $today = date("Y-m-d");
      $q .= ' and (d.date >= "'.$where['spec_date'].'"  and d.date <= "'.$today.'")';
    }

    if(!empty($where["min_price"])){
      $q .= ' and d.amount >= '.$where['min_price'].' ';
    }

    if(!empty($where["max_price"])){
      $q .= ' and d.amount <= '.$where['max_price'].' ';
    }

    if(!empty($where["start_date"])){
      $q .= ' and d.date >= "'.$where['start_date'].'" ';
    }

    if(!empty($where["end_date"])){
      $q .= ' and d.date <= "'.$where['end_date'].' "';
    }

    if(!empty($where) && array_key_exists("sub_type_ids", $where)){
      $q .= ' and (';
      foreach($where["sub_type_ids"] as $sub_type_id){
        $q .= '  d.sub_type_id = '.$sub_type_id .' or ';
      }
      $q = substr($q, 0, -3).')';
    }

    if(!empty($where["text"])){
      $q .= ' and (d.amount like  "%'.$where['text'].'%" or d.name like  "%'.$where['text'].'%"
      or d.notes like  "%'.$where['text'].'%" or d.url like  "%'.$where['text'].'%") ';
    }

    $q .= ' order by date desc, id';

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
        } else if (!empty($result['uid']) && !empty($result['super_name'])) {
          require_once('DailyExpense/DailyCategory/' . $result["super_name"] . '.php');
          if (file_exists('./UsersDailyExpense/' . $user_id . '/' . $result['super_name'] . '/' . $result['sub_name'] . '.php'))
            require_once('./UsersDailyExpense/' . $user_id . '/' . $result['super_name'] . '/' . $result['sub_name'] . '.php');
          $record = new $result["sub_name"]();
        }
        $record->setUserID($user_id);
        if (!empty($result["record_name"]))
          $record->setName($result["record_name"]);
        if (!empty($result["super_name"]))
          $record->setCategory($result["super_name"]);
        if (!empty($result["sub_name"]))
          $record->setsubName($result["sub_name"]);
        if (!empty($result["payment_name"]))
          $record->setPaymentName($result["pay_name"]);
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
        if (!empty($result["amount"]))
          $record->setAmount($result["amount"]);
        if (!empty($result["supertypeid"])) {
          $record->setSuperID($result["supertypeid"]);
        }

        $res[$record->getRecordID()] = $record;
      }
      return $res;
    }
  }

  public function setCategory($cate)
  {
    $this->_category = $cate;
  }

  public function getCategory()
  {
    return $this->_category;
  }

  public function setName($name)
  {
    $this->_name = $name;
  }

  public function getName()
  {
    return $this->_name;
  }

  public function setsubName($name)
  {
    $this->_sub_name = $name;
  }

  public function getsubName()
  {
    $this->_sub_name = str_replace('_', ' ', $this->_sub_name);
    return $this->_sub_name;
  }

  public function setPaymentName($name)
  {
    $this->_payment_name = $name;
  }

  public function getPaymentName()
  {
    return $this->_payment_name;
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
    $q = 'select *
from sandbox.dailysubtypes dy
join sandbox.dailysupertypes dyy on dy.dailysupertypes = dyy.id
where (dy.user_id is null) ';
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

  public function setIsTemp($bool)
  {

  }

  public function getIsTemp()
  {
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
    if (!empty($this->_id)) {
      return $this->_id;
    } else
      return false;

  }

  public function getUrl()
  {
    if (!empty($this->_url))
      return $this->_url;
    else
      return;
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

  public function setAmount($amount)
  {
    $this->_amount = $amount;
  }

  public function getAmount()
  {
    return $this->_amount;
  }

  public function getSubTypeID()
  {
    if (!empty($this->_sub_type_id))
      return $this->_sub_type_id;
    else
      return false;
  }

  public static function removeRecord($id)
  {
    global $pdo_dbh;
    $q = 'delete
  from sandbox.daily_record
  where id = '.$id;
    $statement = $pdo_dbh->prepare($q);
    $statement->execute();
    return;
  }



}

?>