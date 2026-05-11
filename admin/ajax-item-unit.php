<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass('ItemUnit.class.php');
$itemUnit = createObjAndAddToCol(new ItemUnit());

$obj = $itemUnit;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>