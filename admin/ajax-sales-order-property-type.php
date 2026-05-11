<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('SalesOrderPropertyType.class.php');
$salesOrderPropertyType = createObjAndAddToCol(new SalesOrderPropertyType());

$obj = $salesOrderPropertyType;  

$fieldValue = $obj->tableName.'.statuskey';
 
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';

die;
  
?>