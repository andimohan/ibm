<?php
require_once '../../_config.php';  
require_once '_include.php';


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderSubscription.class.php';
$salesOrderSubscription = new SalesOrderSubscription(); 
$url = API_URL.'sales-order-subscription'; 
//$salesOrderSubscription->setLog($_POST['data'],true);

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $salesOrderSubscription->executeImportAPI($url,$_POST['data'], 'code');

?>