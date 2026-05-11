<?php    
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

// cari ad marketplace aktif gk, kal og kad gk usah proses;
if(!$class->hasActiveMarketplace()) die;

includeClass(array('Marketplace.class.php',  
                   'Item.class.php', 
                   'Customer.class.php', 
                   'SalesOrder.class.php',  
                   'TermOfPayment.class.php', 
                   'Shipment.class.php', 
                   'AR.class.php', // utk validasi di JO
                   'PaymentConfirmation.class.php',  //  utk validasi di JO
                   'RewardsPoint.class.php', //  utk validasi di JO
                   'ItemMovement.class.php', //  utk validasi di JO
                   'SalesDelivery.class.php', // utk validasi di JO
                   'GeneralJournal.class.php',
                  ));

$marketplace = createObjAndAddToCol( new Marketplace()); 
 
$marketplace->importOrdersInAllMarketplace(); 
$marketplace->closeCompletedOrdersInAllMarketplace(); 
$marketplace->cancelCanceledOrdersInAllMarketplace();
?>