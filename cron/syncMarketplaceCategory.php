<?php    
require_once '../_config.php'; 
require_once "../_include-v2.php"; 

includeClass(array('Marketplace.class.php','Category.class.php','ItemCategory.class.php','Item.class.php')); 
$marketplace = new Marketplace(); 
 
$marketplace->syncAllMarketplaceCategory(1);  
$marketplace->setLog("import category complete",true); 

?>