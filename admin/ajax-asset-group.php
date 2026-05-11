<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('AssetGroup.class.php');
$assetGroup = createObjAndAddToCol(new AssetGroup());

$obj = $assetGroup;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

$fieldValue = array('code', 'name') ;

include 'ajax-general.php';
 
die;
  
?>