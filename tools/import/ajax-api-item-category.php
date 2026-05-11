<?php
require_once '../../_config.php';  
require_once '_include.php';
  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemCategory.class.php';      

$itemCategory = new ItemCategory();   
$url = API_URL.'item-categories';

echo $itemCategory->executeImportAPI($url,$_POST['data'], 'code');

?>