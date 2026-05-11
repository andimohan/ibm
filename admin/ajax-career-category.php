<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CareerCategory.class.php');
$careerCategory = createObjAndAddToCol(new CareerCategory());  

$obj = $careerCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>