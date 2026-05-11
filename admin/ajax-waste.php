<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Waste.class.php');
$waste = createObjAndAddToCol(new Waste());

$obj = $waste;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>