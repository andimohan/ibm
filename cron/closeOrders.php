<?php    
require_once '../_config.php'; 
require_once '../_include.php'; 

$class->setLog("start closing " . date('d / m / Y H:i:s'),true,'mp');
$marketplace->closeCompletedOrdersInAllMarketplace();
$class->setLog("closing finish" . date('d / m / Y H:i:s'),true,'mp');

echo 'done';
?>