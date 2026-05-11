<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';  
require_once '_global.php';  


includeClass(array('Customer.class.php'));

$customer = new Customer(); 

$obj = $customer;
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

include 'ajax-general.php';
  
die;
  
?>
