<?php
require_once '../../_config.php';  
require_once '_include.php';


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php';
$emklPurchaseOrderImport = new EMKLPurchaseOrder(EMKL['jobType']['import']); 

$url = API_URL.'ff-purchase-order-import';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $emklPurchaseOrderImport->executeImportAPI($url,$_POST['data'], 'code');

?>