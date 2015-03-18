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
session_start();
require_once("DailyExpense/DailyExpense.php");
require_once("DailyExpense/Users.php");
require_once("DailyExpense/Comments.php");
if (!empty($_SESSION['daily']['user_id'])) {
  $records = DailyExpense::generateObjects($_SESSION['daily']['user_id'], false, NULL);
} else {
  require_once("DailyExpense/UsersTemp.php");
  $usertemp = new UsersTemp(md5(get_client_ip_server()));
  $usertemp->CheckUser();
  $bool = $usertemp->getIsInSystem();
  if ($bool == false) {
    require_once("db_abstract.php");
    $layer = new db_abstract_layer();
    $data = array("user_id" => '"' . $usertemp->getUserId() . '"');
    $_SESSION['daily']['temp_user_id'] = $layer->inserting($data, "users_temp");
  } else
    $_SESSION['daily']['temp_user_id'] = $usertemp->getID();
    $records = DailyExpense::generateObjects($_SESSION['daily']['temp_user_id'], true, NULL);
}
$books = array(
    '@attributes' => array(
        'type' => 'fiction'
    ),
    'book' => 1984
);
if(!empty($records)) {
  $res = array();
  foreach ($records as $value) {
    $res[$value->getDate()][] = $value;
  }

  $result = array();
  foreach($res as $date => $records){
    foreach($records as $key => $record){
      $result[$date][$key]["user_id"] = $record->getUserID();
      $result[$date][$key]["note"] = $record->getNote();
      $result[$date][$key]["amount"] = $record->getAmount();
      $result[$date][$key]["amount"] = $record->getAmount();
    }
  }

  echo '<pre>';
  print_r($result);
  echo '</pre>';
}


//header ("Content-Type:text/xml");
//$xml = Array2XML::createXML('root', $books);
//echo $xml->saveXML();
?>