<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('TruckingJob.class.php'));
$truckingJob = new TruckingJob();

$obj = $truckingJob;

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>