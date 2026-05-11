<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemIn.class.php';
$itemIn = new ItemIn(); 

$url = API_URL.'item-in';
   
echo $itemIn->executeImportAPI($url,$_POST['data'], 'code');
?>