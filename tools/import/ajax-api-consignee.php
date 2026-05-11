<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';    

$consignee = new Consignee();   

$url = API_URL.'consignees';
  
// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $consignee->executeImportAPI($url,$_POST['data'], 'code');

?>