<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CostGrouping.class.php');
$costGrouping = createObjAndAddToCol(new CostGrouping());

$obj = $costGrouping;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');

include 'ajax-general.php';

die;
