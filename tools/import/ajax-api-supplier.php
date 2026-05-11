<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';    

$supplier = new Supplier();   

$url = API_URL.'suppliers';
  
// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $supplier->executeImportAPI($url,$_POST['data'], 'code');

?>