<?php
require_once '../_config.php';
require_once '../_include-v2.php';


includeClass('CarServiceMaintenanceRequest.class.php');
$carServiceMaintenanceRequest = createObjAndAddToCol(new CarServiceMaintenanceRequest());

$obj = $carServiceMaintenanceRequest;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true)) ;

$addDataFile = 'carServiceMaintenanceRequestForm';


function generateQuickView($obj, $id)
{
   $item = new Item();

   $detail = '';

   return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
?>