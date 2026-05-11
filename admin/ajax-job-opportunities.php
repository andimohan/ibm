<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('JobOpportunities.class.php');
$jobOpportunities = createObjAndAddToCol(new JobOpportunities());  

$obj = $jobOpportunities;    

$arrCriteria = array();  
//array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

$fieldValue = $obj->tableName.'.title';

include 'ajax-general.php';
 
die;
  
?>