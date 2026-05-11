<?php

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('BiteShip.class.php','Shipment.class.php'));

$biteship = new BiteShip();
$shipment = new Shipment();

$response = $biteship->getAllCouriers();

$arrCourier = array();

// group per jasa kirim dulu
foreach($response['couriers'] as $row){
    $code = $row['courier_code'];
    $name = $row['courier_name'];
    $servicename = $row['courier_service_name'];
    $servicecode = $row['courier_service_code'];
    
    $sameday = ($row['service_type'] == 'same_day') ? 1 : 0;
    
    if (!isset($arrCourier[$code])) {
        $arrCourier[$code] = array('code' => $code, 'name' => $name, 'needlocation' => $sameday, 'services' => array()); 
    }
     
    array_push($arrCourier[$code]['services'], array('servicecode' => $servicecode, 'servicename' => $servicename, 'issameday' => $sameday));
    
}

try{ 

    $class->oDbCon->startTrans(); 

    //$class->setLog($arrCourier,true);
    
    foreach( $arrCourier as $row){
        $arrParam = array();
        $arrParam['code']  = $row['code'];
        $arrParam['name']  = $row['name'];
        $arrParam['selStatus']  = 1;
        $arrParam['chkDropOffLocation']  = $row['needlocation'];
        
        $arrParam['hidDetailKey'] = array();
        //$arrParam['refkey'] = array();
        $arrParam['serviceCode'] = array();
        $arrParam['serviceName'] = array();
        $arrParam['chkSameDay'] = array();
        
        foreach($row['services'] as $serviceRow){
            array_push($arrParam['hidDetailKey'],0);
            //array_push($arrParam['refkey'],0);
            array_push($arrParam['serviceCode'],$serviceRow['servicecode']);
            array_push($arrParam['serviceName'],$serviceRow['servicename']);
            array_push($arrParam['chkSameDay'],$serviceRow['issameday']);
        }
        
        $shipment->addData($arrParam);
    }

    $class->oDbCon->endTrans(); 

} catch(Exception $e){
    $class->oDbCon->rollback();
    var_dump($e->getMessage());
}	


echo '<br>done';
die;

?>