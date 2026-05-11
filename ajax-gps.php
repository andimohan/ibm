<?php  

// gk perlu sementara karena sudah ad secruity login di _include_portal.php
//if(!isset($_GET['token']) || empty($_GET['token'])) return;

require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSConnection.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

if(!isset($_GET['registrationnumber']) || empty($_GET['registrationnumber'])) return;


// perlu ad token khusus 
$registrationNumber = $_GET['registrationnumber'];
 
$gps = new GPSConnection($CUSTOMER_CONN);

// gk perlu, diclassnya sudah otomatis bisa search batch dan per vendor

// sementara, ambil yg bisa batch dulu
//$car = new Car();
//$rsCar = $car->searchData($car->tableName.'.statuskey',1,true, 'and '.$car->tableName.'.gpstrackerid <> "" and '.$car->tableName.'.gpskey <> "" '); 

//$arrCarBatchKey = array(); 
//foreach($rsCar as $row){ 
//	$gpsObj = $gps->getGPSObj(strtolower($row['gpsprovidername'])); 
//	if ($gpsObj->opt['getAllVehicle']) {
//		array_push($arrCarBatchKey,$car->normalizePoliceNumber($row['policenumber']));
//	}
//}
//
//$gpsObj = $gps->getGPSObj(strtolower($provider));  
//$gpsObj->oDbCon =  $CUSTOMER_CONN; 

//// harus kirimnya nopol, karena gk semua API pake idgps
$gpsData = $gps->getData($registrationNumber);

echo json_encode($gpsData);
die;
?>