<?php    
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass(array('RoutineCost.class.php'));

$routineCost = new RoutineCost();
$obj = $routineCost;
  
$systemTask = (isset($_POST['mnv-cron'])) ? true : false;
      
// kalo bkn dr cron system, cek akses dulu
if (!$systemTask) 
    if(!$security->isAdminLogin($obj->securityObject,10,true)); 
    
$arrPkey = array();
$arrPkey = (isset($_POST) && !empty($_POST['pkey'])) ? json_decode($_POST['pkey']) : array();

$obj->runCron($arrPkey,$systemTask);
?>