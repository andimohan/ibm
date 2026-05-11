<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('TruckingAdditionalCost.class.php'));
$truckingAdditionalCost = new TruckingAdditionalCost();

$obj = $truckingAdditionalCost;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class
if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'truckingAdditionalCostForm';
$quickView = false;

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj, $id)
{
    $detail = '';
    return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');

?>