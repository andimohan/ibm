<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CareerField.class.php');
$careerField = createObjAndAddToCol(new CareerField());  

$obj = $careerField;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>