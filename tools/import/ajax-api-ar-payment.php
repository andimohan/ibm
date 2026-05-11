<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPayment.class.php'; 

$arPayment = new ARPayment();

$url = API_URL.'ar-payment';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT

echo $arPayment->executeImportAPI($url,$_POST['data'], 'code');

?>