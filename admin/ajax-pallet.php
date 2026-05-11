<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Pallet.class.php');
$pallet = createObjAndAddToCol(new Pallet());

$obj = $pallet;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>