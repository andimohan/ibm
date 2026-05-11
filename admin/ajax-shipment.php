<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $shipment;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';

switch ($_POST['action']){ 
	case 'getShippingInformation' : 
	        $serviceKey = $_POST['serviceKey'];
	        $fromCityKey = $_POST['fromCityKey'];
	        $toCityKey = $_POST['toCityKey'];
	        $useInsurance = $_POST['useInsurance'];
	        $totalValue = $_POST['totalValue'];
	        $weight = (isset($_POST['weight']) && !empty($_POST['weight'])) ? $_POST['weight'] : 1;
        
            //$class->setLog($_POST,true);
        
            $rs = $obj->getShippingInformation($serviceKey,$fromCityKey ,$toCityKey,$weight,$totalValue,$useInsurance);
            //$class->setLog($rs,true);
            echo json_encode($rs); 
            break;

	case 'getAvailableShippingServices' : 
	     
	        $fromCityKey = $_POST['fromCityKey'];
	        $toCityKey = $_POST['toCityKey'];
        
            $rs = $obj->getAvailableShippingServices($fromCityKey ,$toCityKey);
            //$class->setLog($rs,true);
            echo json_encode($rs); 
            break;

}

die;
  
?>
