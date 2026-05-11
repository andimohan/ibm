<?php  
require_once '../../_config.php'; 

/*require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Security.class.php';  
$escurity = new Security();*/

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSETI.class.php';  
$gps = new GPSETI();
  
//if(!$security->isAdminLogin($gps->securityObject,10,true)); 

if(!isset($_GET['policenumber']) || empty($_GET['policenumber']))  return;

$policenumber = $_GET['policenumber'];

$gpsData = $gps->getData($policenumber);
  
echo json_encode($gpsData);
die;
?>