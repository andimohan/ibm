<?php

// AJAX untuk narik kendaraan berdasarkan SPK
// kalo gk ad SPK berarti gk ad kendaraan yg direturn

require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';
 
$class->setLog("perlu token khusus",true);

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSConnection.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

$gps = new GPSConnection($CUSTOMER_CONN); // khusus GPS Connecttion, langsung kirim parameter
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$truckingServiceWorkOrder->oDbCon = $CUSTOMER_CONN;

// kalo pertama kirim wokeynya
// kemungkinana ad issue, kalo ada beberapa SPK menggunakan nopol yg sama
$rsSPK = array();

//kedepanya buat bisa narik json / array
if (isset($_GET['wokey']) && !empty($_GET['wokey'])){
	
	// sementara pake status SPK = 2 
	$rsSPK = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.pkey',$_GET['wokey'], true, ' and '.$truckingServiceWorkOrder->tableName.'.statuskey = 2');
	$registrationNumber = $rsSPK[0]['policenumber'];
	
	$rsSPK = array_column($rsSPK,null,$registrationNumber);
	
}else{
	$registrationNumber = (isset($_GET['registrationnumber']) && !empty($_GET['registrationnumber'])) ?  $_GET['registrationnumber'] : array();
}

//// harus kirimnya nopol, karena gk semua API pake idgps
$gpsData = $gps->getData($registrationNumber);

// biar unique
$gpsData = array_column($gpsData,null,'policenumber');

// update isi ulang dengan informasi SPK nya 
foreach($rsSPK as $key=>$row){
	$rsSPK[$key]['gps'] = $gpsData[str_replace(' ' ,'',$row['policenumber'])];
}

//$gps->setLog($gpsData,true);
//echo json_encode($gpsData);
echo json_encode($rsSPK);
die;

?>