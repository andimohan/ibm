<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Diagnose.class.php');
$diagnose = createObjAndAddToCol(new Diagnose());

$obj = $diagnose;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1'); 

$order = ' order by ' .$obj->tableName.'.code asc';

$fieldValue = array('code','name');

include 'ajax-general.php';
 
die;
  
?>