<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php';


includeClass(array('Marketplace.class.php'));
$shopee = new Shopee();

// digunakan utk temp mengecek item apa saja yg suda keupload
echo $shopee->createAuthLink();

?>