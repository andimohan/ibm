<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Depot.class.php');
$depot = createObjAndAddToCol(new Depot());

$obj = $depot;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>