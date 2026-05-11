<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';    

$customer = new Customer();  

// pake ini utk transfer obj ke class, karena yg dikirim cuma reftabletype
$customerCategory =  createObjAndAddToCol(new CustomerCategory());
//$city =  createObjAndAddToCol(new City());  

$url = API_URL.'customers';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $customer->executeImportAPI($url,$_POST['data'], 'code');

?>