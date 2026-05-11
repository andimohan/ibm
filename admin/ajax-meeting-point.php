<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass('MeetingPoint.class.php');
$location = createObjAndAddToCol(new MeetingPoint());

$obj = $location;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>