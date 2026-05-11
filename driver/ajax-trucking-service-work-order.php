<?php 
require_once '../_config.php'; 
require_once '../_include.php';
require_once '_global.php';  

if(!isset($_POST) || empty($_POST['action'])) die;
if(empty($class->userkey)) die; // harusnya sudah ad validasi di global, !empty session

$arrayToJs = array(); 

switch ($_POST['action']){ 
	case 'proceedWorkOrder' :  
                //cek SPK dan user sama tidak
      
                $workOrderId = $_POST['hidWOKey'];
                $carRegistrationNumber =  $_POST['carRegistrationNumber'];
         
				$arrayToJs = $truckingServiceWorkOrder->updateVehicleByRegistrationNumber($workOrderId,$class->userkey,$carRegistrationNumber); 
				break;
		 
    case 'takeWorkOrder'  : 
        
                $workOrderCode = $_POST['woCode'];
                $verificationCode =  $_POST['verificationCode'];
         
                $arrayToJs = $truckingServiceWorkOrder->takeWorkOrder($workOrderCode,$class->userkey,$verificationCode);
                break;    
        
        
    case 'getDataRowById'  : 
        
                $workOrderCode = $_POST['code']; 
         
                $arrayToJs = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.code',$workOrderCode,true);
                break;    
            
}


echo json_encode($arrayToJs); 

?>
