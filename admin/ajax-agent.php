<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $agent;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   
array_push ($arrCriteria, $obj->tableName.'.suppliertype = '.$obj->supplierType);   

include 'ajax-general.php';
 
die;
  
?>