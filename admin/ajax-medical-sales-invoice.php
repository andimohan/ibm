<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('MedicalSalesInvoice.class.php');
$medicalSalesInvoice = createObjAndAddToCol(new MedicalSalesInvoice());

$obj = $medicalSalesInvoice;   

// $arrCriteria = array();  
// array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  
$fieldValue = $obj->tableName . '.code';

include 'ajax-general.php';

die;
  
?>