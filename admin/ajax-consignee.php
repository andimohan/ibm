<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Consignee.class.php');
$consignee = createObjAndAddToCol(new Consignee());
 
$obj = $consignee;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>
