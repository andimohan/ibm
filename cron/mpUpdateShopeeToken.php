<?php     
// ini per domain

require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Marketplace.class.php'));

$marketplace = createObjAndAddToCol( new Marketplace()); 
$marketplace->updateTokenShopee();

?>