<?php
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  
	  
	$arrReturn = array(); 
	$arr['createdBy'] = 0;
	$arr['selStatus'] = 2;
    $arr['fromFE'] = 1;
	$arrReturn = $testimonial->addData($arr);
 	 
	echo json_encode($arrReturn);  
	die; 
	
?>