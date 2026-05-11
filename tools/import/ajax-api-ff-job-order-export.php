<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php';
$emklJobOrderExport = new EMKLJobOrder(EMKL['jobType']['export']); 

$url = API_URL.'ff-job-order-export';
   
echo $emklJobOrderExport->executeImportAPI($url,$_POST['data'], 'code');

?>