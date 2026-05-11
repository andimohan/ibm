<?php  

require_once '../_config.php'; 
require_once '../_include-v2.php';  
 
includeClass(array('TruckingServiceWorkOrder.class.php'));
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$obj = $truckingServiceWorkOrder;   


if (isset($_POST) && !empty($_POST['action'])) {

	foreach ($_POST as $k => $v) {
		if (!is_array($v))
			$v = trim($v);
		$arr[$k] = $v;
	}

	$arrReturn = array();
	switch ($_POST['action']) {
		case 'updateAssignDriver':
			$arrReturn = $truckingServiceWorkOrder->updateAssignDriver($arr);
			break;
	}
	echo json_encode($arrReturn);
	die;
}
?>