<?php
/**
 * Created by PhpStorm.
 * User: Tian
 * Date: 2015/2/1
 * Time: 17:22
 */
require_once("db.php");

class db_abstract_layer extends Database
{
    public function inserting($data){
      if(!empty($data)){
        $q = 'insert into sandbox.daily_record(';
        $q2 ='values(';
        foreach($data as $key => $value){
          $q .= $key.', ';
          $q2 .= $value.', ';
        }
        $q = substr($q, 0, -2).')';
        $q = $q.$q2;
        $q = substr($q, 0, -2).')';
        $statement = $this->_connection->prepare($q);
        $statement->execute();
      }
    }

    public function updating($data){

    }



}



?>
