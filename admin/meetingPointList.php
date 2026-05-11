<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('MeetingPoint.class.php');
$obj = createObjAndAddToCol(new MeetingPoint());

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'meetingPointForm';

$quickView = false;
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj, $id){
	return '';
}


include('dataList.php');