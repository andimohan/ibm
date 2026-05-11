<?php     
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

// cari ad marketplace aktif gk, kal og kad gk usah proses;
if(!$class->hasActiveMarketplace()) die;

includeClass(array('Marketplace.class.php','SalesOrder.class.php','Shipment.class.php'));

$marketplace = createObjAndAddToCol( new Marketplace());  
$shopee = new Shopee();
$shopee->updateLogistic(); 
?>