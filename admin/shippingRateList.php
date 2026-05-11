<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('ShippingRate.class.php');
$shippingRate = createObjAndAddToCol(new ShippingRate());

$obj = $shippingRate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class
if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'shippingRateForm';


$quickView = false;
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';


function generateQuickView($obj, $id)
{
	
	$detail = '';
	return $detail;
}

// die;
// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
