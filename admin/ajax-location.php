<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass('Location.class.php');
$location = createObjAndAddToCol(new Location());

$obj = $location;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>