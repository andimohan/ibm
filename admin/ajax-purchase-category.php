<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
 
includeClass('PurchaseCategory.class.php');
$purchaseCategory = createObjAndAddToCol(new PurchaseCategory());

$obj = $purchaseCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');    

include 'ajax-general.php';
 
die;
  
?>