<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Vessel.class.php');
$vessel = createObjAndAddToCol(new Vessel());

$obj = $vessel;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>