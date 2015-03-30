<?php
class Array2XML {

  private static $xml = null;
  private static $encoding = 'UTF-8';

  /**
   * Initialize the root XML node [optional]
   * @param $version
   * @param $encoding
   * @param $format_output
   */
  public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
    self::$xml = new DomDocument($version, $encoding);
    self::$xml->formatOutput = $format_output;
    self::$encoding = $encoding;
  }

  /**
   * Convert an Array to XML
   * @param string $node_name - name of the root node to be converted
   * @param array $arr - aray to be converterd
   * @return DomDocument
   */
  public static function &createXML($node_name, $arr=array()) {
    $xml = self::getXMLRoot();
    $xml->appendChild(self::convert($node_name, $arr));

    self::$xml = null;    // clear the xml node in the class for 2nd time use.
    return $xml;
  }

  /**
   * Convert an Array to XML
   * @param string $node_name - name of the root node to be converted
   * @param array $arr - aray to be converterd
   * @return DOMNode
   */
  private static function &convert($node_name, $arr=array()) {

    //print_arr($node_name);
    $xml = self::getXMLRoot();
    $node = $xml->createElement($node_name);

    if(is_array($arr)){
      // get the attributes first.;
      if(isset($arr['@attributes'])) {
        foreach($arr['@attributes'] as $key => $value) {
          if(!self::isValidTagName($key)) {
            throw new Exception('[Array2XML] Illegal character in attribute name. attribute: '.$key.' in node: '.$node_name);
          }
          $node->setAttribute($key, self::bool2str($value));
        }
        unset($arr['@attributes']); //remove the key from the array once done.
      }

      // check if it has a value stored in @value, if yes store the value and return
      // else check if its directly stored as string
      if(isset($arr['@value'])) {
        $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
        unset($arr['@value']);    //remove the key from the array once done.
        //return from recursion, as a note with value cannot have child nodes.
        return $node;
      } else if(isset($arr['@cdata'])) {
        $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
        unset($arr['@cdata']);    //remove the key from the array once done.
        //return from recursion, as a note with cdata cannot have child nodes.
        return $node;
      }
    }

    //create subnodes using recursion
    if(is_array($arr)){
      // recurse to get the node for that key
      foreach($arr as $key=>$value){
        if(!self::isValidTagName($key)) {

          throw new Exception('[Array2XML] Illegal character in tag name. tag: '.$key.' in node: '.$node_name);
        }
        if(is_array($value) && is_numeric(key($value))) {
          // MORE THAN ONE NODE OF ITS KIND;
          // if the new array is numeric index, means it is array of nodes of the same kind
          // it should follow the parent key name
          foreach($value as $k=>$v){
            $node->appendChild(self::convert($key, $v));
          }
        } else {
          // ONLY ONE NODE OF ITS KIND
          $node->appendChild(self::convert($key, $value));
        }
        unset($arr[$key]); //remove the key from the array once done.
      }
    }

    // after we are done with all the keys in the array (if it is one)
    // we check if it has any text value, if yes, append it.
    if(!is_array($arr)) {
      $node->appendChild($xml->createTextNode(self::bool2str($arr)));
    }

    return $node;
  }

  private static function getXMLRoot(){
    if(empty(self::$xml)) {
      self::init();
    }
    return self::$xml;
  }
  private static function bool2str($v){
    //convert boolean to text value.
    $v = $v === true ? 'true' : $v;
    $v = $v === false ? 'false' : $v;
    return $v;
  }
  private static function isValidTagName($tag){
    $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
    return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
  }
}
$data = array();
if(!empty($_POST['user_id'])){

  if(!preg_match('/^[0-9]+$/', $_POST['user_id'])){
    echo 'User ID is not valid.';
    exit;
  }

  if(empty($_POST['is_temp']))
    $bool = false;
  else
    $bool = true;

  $data = array();
  if(!empty($_POST['sub_type_ids']))
    $data["sub_type_ids"] = $_POST['sub_type_ids'];

  if(!empty($_POST['min_price']))
    $data["min_price"] = $_POST['min_price'];

  if(!empty($_POST['max_price']))
    $data["max_price"] = $_POST['max_price'];


  if(!empty($_POST['start_date']))
    $data["start_date"] = $_POST['start_date'];

  if(!empty($_POST['end_date']))
    $data["end_date"] = $_POST['end_date'];

  if(!empty($_POST['spec_date'])){
    $data["spec_date"] = $_POST['spec_date'];
  }

  if(!empty($_POST['text'])){
    $data["text"]= $_POST['text'];
  }


  if(!empty($_POST)){
    $sub_type_ids = array();
    foreach($_POST as $key => $value){
      if(preg_match('/^sub\_type\_id\_[0-9]+$/', $key, $info)){
        $data["sub_type_ids"][] = $value;
      }
    }
  }

  require_once("DailyExpense/DailyExpense.php");
  $records = DailyExpense::generateObjects($_POST['user_id'], $bool, $data);
  if(!empty($records)) {
    $res = array();
    foreach ($records as $value) {
      $res[$value->getDate()][] = $value;
    }
    $result = array();
    foreach($res as $date => $records)  {
      foreach($records as $key => $record)  {
        $result[$date]['record_'.$key]["user_id"] = $record->getUserID();
        $result[$date]['record_'.$key]["note"] = $record->getNote();
        $result[$date]['record_'.$key]["amount"] = $record->getAmount();
        $result[$date]['record_'.$key]["name"] = $record->getName();
        $result[$date]['record_'.$key]["id"] = $record->getRecordID();
        $result[$date]['record_'.$key]["superid"] = $record->getSuperID();
        $result[$date]['record_'.$key]["paymentid"] = $record-> getPaymentID();
        $result[$date]['record_'.$key]["subtypeid"] = $record-> getSubTypeID();
        $result[$date]['record_'.$key]["url"] = $record-> getURL();
        $result[$date]['record_'.$key]["date"] = $record-> getDate();
      }
    }
    header ("Content-Type:text/xml");
    $xml = Array2XML::createXML('root', $result);
    echo $xml->saveXML();
  }
}
else{
  echo 'No user id is given.';
  exit;
}
?>