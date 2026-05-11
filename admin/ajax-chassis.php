<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Chassis.class.php');
$chassis = new Chassis();

$obj = $chassis;  
$fieldValue = $obj->tableName.'.chassisnumber'; 

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>