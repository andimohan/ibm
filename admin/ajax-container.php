<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Container.class.php');
$container = createObjAndAddToCol(new Container());

$obj = $container;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>