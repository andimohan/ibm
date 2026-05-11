<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TemplateSupplier.class.php');
$templateSupplier = createObjAndAddToCol(new TemplateSupplier()); 

$obj = $templateSupplier;
include 'ajax-general.php';
 
die;
  
?>
