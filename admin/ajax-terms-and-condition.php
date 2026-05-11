<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('TermsAndConditions.class.php');
$termsAndConditions = createObjAndAddToCol(new TermsAndConditions());

$obj = $termsAndConditions;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>