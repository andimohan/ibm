<?php
require_once '../../_config.php';  
require_once '_include.php';
  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';      

$brand = new Brand();   
$url = API_URL.'brands';

echo $brand->executeImportAPI($url,$_POST['data'], 'code');

?>