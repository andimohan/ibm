<?php    
require_once '../_config.php'; 
require_once "../_include.php"; 

ini_set('max_execution_time', 300);

$marketplace->syncAllMarketplaceCategoryAttributes(1); // tipe sync 2 blm ditest !!
echo 'import category attributes complete';
$marketplace->setLog("import category attributes complete"); 
?>