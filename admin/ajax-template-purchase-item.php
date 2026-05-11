<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';   

includeClass(array('TemplateEMKLPurchaseItem.class.php'));

$templateEMKLPurchaseItem = new TemplateEMKLPurchaseItem();	
$obj = $templateEMKLPurchaseItem;    
 
include 'ajax-general.php'; 
 
die;
  
?>
