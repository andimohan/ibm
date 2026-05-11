<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';
    
$tokopedia->syncMarketplaceStorefront();
echo 'done';
die; 
?>