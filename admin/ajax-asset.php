<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Asset.class.php');
$asset = createObjAndAddToCol(new Asset());

$obj = $asset;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

$fieldValue = array('code', 'name') ;

if(isset($_GET) &&  $_GET['hasBookValue'] == 1){ 
    unset($_GET['hasBookValue']);
    array_push ($arrCriteria, ' round('.$obj->tableName.'.bookvalue) > 0 ');  
}

include 'ajax-general.php';
 
die;
  
?>