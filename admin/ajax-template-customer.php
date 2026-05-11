<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('TemplateCustomer.class.php');
$templateCustomer = createObjAndAddToCol(new TemplateCustomer()); 
 
$obj = $templateCustomer;   
 
include 'ajax-general.php';
  
die;
  
?>
