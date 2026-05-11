<?php  

require_once '../../_config.php'; 
require_once '../../_include-v2.php';  
 
includeClass(array('TruckingServiceWorkOrder.class.php'));
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$obj = $truckingServiceWorkOrder;   

$workProgres = $truckingServiceWorkOrder->getWorkProgress(); 
echo json_encode($workProgres);
 
die;
?>