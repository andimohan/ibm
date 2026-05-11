<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('GoodCorporateGovernmentReport.class.php');
$goodCorporateGovernmentReport = createObjAndAddToCol(new GoodCorporateGovernmentReport());

$obj = $goodCorporateGovernmentReport;   
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

$fieldValue = $obj->tableName.'.title';
include 'ajax-general.php';
 
die;
  
?>