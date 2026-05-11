<?php 
require_once '../_config.php'; 
require_once '../_include.php';
require_once '_global.php';  
 
if(!isset($_POST) || empty($_POST['action']))
	die;
	  
$arrayToJs = array(); 

switch ( $_POST['action']){ 
	case 'updateProgress' :  
                $arr = array(); 
				foreach ($_POST as $k => $v) { 
					$arr[$k] = $v;
				} 
				 
				$arrayToJs = $workProgress->updateWorkProgress($arr);  
				break;
		 
	
}


echo json_encode($arrayToJs); 

?>
