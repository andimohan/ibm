<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('CarServiceMaintenanceRequest.class.php'));
$carServiceMaintenanceRequest = createObjAndAddToCol(new CarServiceMaintenanceRequest()); 
  
$obj = $carServiceMaintenanceRequest;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

die;
  
?>
