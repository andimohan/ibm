<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('SalesRentalQuotation.class.php');
$salesRentalQuotation = createObjAndAddToCol(new SalesRentalQuotation());   

$obj = $salesRentalQuotation;   

$fieldValue = $obj->tableName.'.code'; 

include 'ajax-general.php';
 
  
?>
