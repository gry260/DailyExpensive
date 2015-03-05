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
    public function inserting($data, $table){
      if(!empty($data)){
        $q = 'insert into sandbox.'.$table.'(';
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
        return $this->_connection->lastInsertId();
      }
      else
        return false;
    }

    public function updating($data, $table, $where_id){
      if(!empty($data)){
        $upd = 'update sandbox.'.$table.' set ';
        foreach($data as $key => $value){
          $upd .= $key.'='.$value.', ';
        }
        $upd = substr($upd, 0, -2).' where ';
        if(!empty($where_id)){
          foreach($where_id as $key => $value){
            $upd .= $key.' = '.$value;
          }
        }
        $statement = $this->_connection->prepare($upd);
        $statement->execute();
        return true;
      }
    }





}



?>
