<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemOut.class.php';
$itemIn = new ItemOut(); 

$url = API_URL.'item-out';
   
echo $itemIn->executeImportAPI($url,$_POST['data'], 'code');
?>