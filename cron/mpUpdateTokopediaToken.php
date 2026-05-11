<?php    
// update token TOKOPEDIA berlaku global, jd gk perlu diupdate per domain

require_once '../_config.php'; 
require_once "../_include-v2.php"; 

includeClass(array('Marketplace.class.php')); 
 
// update AR Payment 
$marketplace = createObjAndAddToCol( new Marketplace()); 
$marketplace->updateTokenTokopedia();  
$marketplace->setLog("token tokopedia updated",true);
?>