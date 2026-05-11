<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CostCashOut.class.php');
$costCashOut = createObjAndAddToCol(new CostCashOut());

$obj = $costCashOut;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>