<?php     
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Marketplace.class.php',   
                   'Item.class.php' 
                  ));

// Boost Items
$marketplace = createObjAndAddToCol( new Marketplace()); 
$marketplace->boostItemInAllMarketplace(); 
//$marketplace->setLog("Boost Item done",true,'cronlog');

?>