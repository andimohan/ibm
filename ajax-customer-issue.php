<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array("CustomerIssue.class.php","SalesOrder.class.php"));
$customerIssue = new CustomerIssue();
$salesOrder = new SalesOrder();

	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  
    
	$arrReturn = array(); 
	$arr['code'] = 'XXXXX';
	$arr['createdBy'] = 0;
	$arr['selStatus'] = 1;
	// $arr['selCategory'] = (!isset($_POST['hidCategoryKey'])) ? $contactCategory->getDefaultData() : $_POST['hidCategoryKey'];
	$arrReturn = $customerIssue->addData($arr);
 	  
	echo json_encode($arrReturn);  
	die; 
	
?>
