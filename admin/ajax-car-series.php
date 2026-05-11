<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CarSeries.class.php');
$carSeries = createObjAndAddToCol(new CarSeries());

$obj = $carSeries;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>