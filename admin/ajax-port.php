<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Port.class.php');
$port = createObjAndAddToCol(new Port());

$obj = $port;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  
 
$_GET['searchField'] = 'name,tag';
$_GET['returnField'] = $obj->tableName.'.name';


include 'ajax-general.php';
 
die;
  
?>