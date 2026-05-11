<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';
 
$class->setLog("perlu token khusus",true);

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSConnection.class.php';
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';

$gps = new GPSConnection($CUSTOMER_CONN); // khusus GPS Connecttion, langsung kirim parameter

$registrationNumber = (isset($_GET['registrationnumber']) && !empty($_GET['registrationnumber'])) ?  $_GET['registrationnumber'] : array();

//// harus kirimnya nopol, karena gk semua API pake idgps
$gpsData = $gps->getData($registrationNumber);

// biar unique
$gpsData = array_column($gpsData,null,'policenumber');

echo json_encode($gpsData);
die;

?>