<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';      

$car = new Car();  
$carCategory =  createObjAndAddToCol(new CarCategory());
$brand =  createObjAndAddToCol(new Brand());
 
$url = API_URL.'cars';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT

echo $car->executeImportAPI($url,$_POST['data'], 'code');

?>