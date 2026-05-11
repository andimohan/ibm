<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Terminal.class.php');
$terminal = createObjAndAddToCol(new Terminal());

$obj = $terminal;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>