<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Packaging.class.php');
$packaging = createObjAndAddToCol(new Packaging());

$obj = $packaging;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>