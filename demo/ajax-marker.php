<?php
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSConnection.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';

$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$securityObject = $truckingServiceWorkOrder->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

//$registrationNumber = $_GET['registrationnumber'];
 
$arrRegistrationNumber = array();
$arrGPSProvider = array();
$arrWarehouse = array();
$arrCarKey = array();
$customerkey = '';
$jobOrderKey = '';

if(isset($_GET['registrationNumber']) && !empty($_GET['registrationNumber']))
    $arrRegistrationNumber =  explode(',',$_GET['registrationNumber']);


if(isset($_GET['gpsProviderKey']) && !empty($_GET['gpsProviderKey'])) {
    $arrGPSProvider = $_GET['gpsProviderKey'];
}

if(isset($_GET['warehousekey']) && !empty($_GET['warehousekey'])) {
    $arrWarehouse = $_GET['warehousekey'];
}

if(isset($_GET['carkey']) && !empty($_GET['carkey'])) {
    $arrCarKey = $_GET['carkey'];
}

if(isset($_GET['customerkey']) && !empty($_GET['customerkey'])) {
    $customerkey = $_GET['customerkey'];
}

if(isset($_GET['jobOrderKey']) && !empty($_GET['jobOrderKey'])) {
    $jobOrderKey = $_GET['jobOrderKey'];
}

$gps = new GPSConnection();
$gpsData = $gps->getData(array(
                            'pkey' => $arrCarKey,
                            'registrationNumber' => $arrRegistrationNumber,
                            'gpsProviderKey' => $arrGPSProvider,
                            'warehousekey' => $arrWarehouse,
    // gk perlu dari query dibawah saja
//                            'customerkey' => $customerkey,
//                            'jobOrderKey' => $jobOrderKey,
                        ));

 
// biar unique
//$gpsData = array_column($gpsData,null,'policenumber');
// update isi ulang dengan informasi SPK nya 
// ambil SPK yg konfirmasi saja, kedepannya gnati SPk yg masi onprogres sopir

$sql = 'select 
            trucking_service_work_order.code as workordercode,
            concat(trucking_service_work_order.routefrom, \' - \', trucking_service_work_order.routeto) as route ,
            employee.name as drivername,
			trucking_service_work_order.carkey,
            replace(car.policenumber,\' \',\'\') as policenumber,
			consignee.name as consigneename
        from 
            trucking_service_work_order
                left join employee on employee.pkey = trucking_service_work_order.driverkey,
            trucking_service_order_header
				left join consignee on trucking_service_order_header.consigneekey = consignee.pkey,
            car
        where
            trucking_service_work_order.refkey = trucking_service_order_header.pkey and
            trucking_service_work_order.carkey = car.pkey and
            trucking_service_work_order.statuskey in (2) and
            trucking_service_order_header.statuskey <> 7
        ';

       //replace(car.policenumber,\' \',\'\') in ('.$class->oDbCon->paramString(array_column($gpsData,'policenumber'),',').') 

    if(!empty($customerkey)) {
        $sql .=' and trucking_service_order_header.customerkey = (' . $class->oDbCon->paramString($customerkey) . ')';
    }

    if(!empty($jobOrderKey)) {
        $sql .=' and trucking_service_work_order.refkey = (' . $class->oDbCon->paramString($jobOrderKey) . ')';
    }

    $sql .=' order by trucking_service_work_order.trdate desc';

$rsSPK = $class->oDbCon->doQuery($sql);
$rsSPK = array_column($rsSPK,null,'carkey');

$hasFilter = !empty($customerkey) || !empty($jobOrderKey); // Cek apakah ada filter

foreach($gpsData as $key => $row){
	
	$gpsData[$key]['drivername'] = '';
	$gpsData[$key]['workordercode'] = '';
	$gpsData[$key]['route'] = '';

    if ($hasFilter) {
        //kalau ada filter, biar mobil yang muncul di map sesuai filter
        if(!isset($rsSPK[$key])) {
            unset($gpsData[$key]);
            continue;
        }
    } else {
        //normal
        if (!isset($gpsData[$key]) || !isset($rsSPK[$key]))
            continue;
    }

    $gpsData[$key]['drivername'] = $rsSPK[$key]['drivername'];
    $gpsData[$key]['workordercode'] = $rsSPK[$key]['workordercode'];
    $gpsData[$key]['route'] = $rsSPK[$key]['route'];
    $gpsData[$key]['consigneename'] = $rsSPK[$key]['consigneename'];
}

echo json_encode($gpsData);
die;

?>