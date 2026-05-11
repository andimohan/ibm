<?php  
require_once '../../_config.php';  
require_once '../../_include-v2.php';  

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSConnection.class.php';

if(!isset($_GET['registrationnumber']) || empty($_GET['registrationnumber'])) return;

$registrationNumber = $_GET['registrationnumber'];
 
$gps = new GPSConnection();

// harus kirimnya nopol, karena gk semua API pake idgps
$gpsData = $gps->getData($registrationNumber);

echo json_encode($gpsData);
die;
?>