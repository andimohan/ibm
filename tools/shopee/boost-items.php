<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('Item.class.php','Marketplace.class.php'));
 
$shopee = new Shopee();

$shopee->boostItem();
echo 'done';
 

?>