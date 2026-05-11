<?php    
require_once '../_config.php'; 
require_once "../_include.php"; 
 
$marketplace->syncAllMarketplaceBrand(1); 
$marketplace->setLog("import brand complete"); 
 
?>