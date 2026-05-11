<?php    
set_time_limit(10000);  
	
require_once '../_config.php'; 
require_once "../_include-v2.php"; 

includeClass(array('Marketplace.class.php'));

$marketplace = createObjAndAddToCol( new Marketplace());  


$marketplace->syncAllMarketplaceCategoryVariant();  
$marketplace->setLog("import category variant complete",true); 

?>