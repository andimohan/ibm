<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Category.class.php','CarCategory.class.php'));
$carCategory = createObjAndAddToCol(new CarCategory());

$obj = $carCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>