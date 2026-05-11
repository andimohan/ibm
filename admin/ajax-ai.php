<?php
require_once '../_config.php';
require_once '../_include-v2.php';
 
includeClass(array('SalesOrder.class.php'));
    
$salesOrder = new SalesOrder();

$opt=array();

$startPeriod = (isset($_POST['startPeriod']) && !empty($_POST['startPeriod'])) ? $_POST['startPeriod'] : 'January ' . date('Y'); 
$endPeriod = (isset($_POST['endPeriod']) && !empty($_POST['endPeriod'])) ? $_POST['endPeriod'] : 'December ' . date('Y'); 
$warehousekey = (isset($_POST['selWarehouse']) && !empty($_POST['selWarehouse'])) ? $_POST['selWarehouse'] : ''; 

$opt['startPeriod'] = $startPeriod;
$opt['endPeriod'] = $endPeriod;
$opt['selWarehouse'] = $warehousekey;

$rsData = $salesOrder->getReportForAI($opt);

echo json_encode($rsData);
die;
?>