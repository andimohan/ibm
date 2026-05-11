<?php
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  


if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 


	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  
	 
	
	$arrReturn = array();  
	  
	$arr['itemKey'] = $arr['itemKey'];  
	$arr['customerKey'] = USERKEY;  
	
	$arrReturn = $emailBlast->addData($arr);
 	 
	echo json_encode($arrReturn);  
	die; 
	
?>