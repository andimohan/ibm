<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass('ManagementTeam.class.php');
$managementTeam = createObjAndAddToCol(new ManagementTeam());

$obj = $managementTeam;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>