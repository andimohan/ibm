<?php     
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Marketplace.class.php',   
                   'Warehouse.class.php',  
                   'SalesOrder.class.php',  
                   'PaymentMethod.class.php', 
                   'AR.class.php', // utk validasi di JO  
                  ));

// update AR Payment
$marketplace = createObjAndAddToCol( new Marketplace()); 
$marketplace->updateARPaymentInAllMarketplace(); 
//$marketplace->setLog("MP AR done",true,'cronlog');

?>