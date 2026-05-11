<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Category.class.php','CSRCategory.class.php'));
$CSRCategory = createObjAndAddToCol(new CSRCategory());
 
$obj = $CSRCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>