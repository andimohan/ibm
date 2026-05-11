<?php
require_once '../_config.php';
require_once '../_include-v2.php';
includeClass('SalesOrderRecurringSubscription.class.php');
$salesOrderRecurringSubscription = createObjAndAddToCol(new SalesOrderRecurringSubscription());

$obj = $salesOrderRecurringSubscription;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class
if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'salesOrderRecurringSubscriptionForm';
 

function generateQuickView($obj, $id)
{
   if (function_exists('customGenerateQuickView'))
      return customGenerateQuickView($obj, $id);

   $detail = '';

   return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
?>