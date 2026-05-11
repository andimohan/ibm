<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';  
require_once '_global.php';  


includeClass(array('ManagementTeam.class.php'));

$managementTeam = new ManagementTeam(); 

$obj = $managementTeam;
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
die;
  
?>
