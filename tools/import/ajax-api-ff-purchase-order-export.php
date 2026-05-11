<?php
require_once '../../_config.php';  
require_once '_include.php';


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php';
$emklPurchaseOrderExport = new EMKLPurchaseOrder(EMKL['jobType']['export']); 

$url = API_URL.'ff-purchase-order-export';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $emklPurchaseOrderExport->executeImportAPI($url,$_POST['data'], 'code');

?>