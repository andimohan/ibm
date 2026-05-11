<?php

require_once '../../_config.php'; 
require_once '_include.php';    

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AR.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php'; 

$arPayment = new AR();

$url = API_URL.'ar';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT

echo $arPayment->executeImportAPI($url,$_POST['data'], 'code');


?>