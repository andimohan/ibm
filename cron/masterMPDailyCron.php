<?php
// khusus database programs_stok saja

// harusnya gk bisa, error pas di dalam _include-v2, coba cek lg

set_include_path('/home/wintera/public_html/');   
require_once '_config.php'; 
require_once '_include-v2.php';  

includeClass('Marketplace.class.php');
$marketplace = new Marketplace();

// update logistic
$marketplace->syncAllMarketplaceLogistics();
$marketplace->setLog("MP Logistic done",true,'cronlog');
?>