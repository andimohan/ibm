<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemAdjustment.class.php';
$itemIn = new ItemAdjustment(); 

$url = API_URL.'item-adjustment';
   
echo $itemIn->executeImportAPI($url,$_POST['data'], 'code');
?>