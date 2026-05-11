<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('CategoryAssetItem.class.php'));
$categoryAssetItem = createObjAndAddToCol(new CategoryAssetItem());

$obj = $categoryAssetItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
   ;

$addDataFile = 'categoryAssetItemForm';
$quickView = false;

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj, $id)
{
   return '';
}


include('dataList.php');

?>