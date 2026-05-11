<?php     
require_once '../_config.php'; 
require_once '../_include-v2.php';

//$class->setLog('['.date("Y/m/d H:i").'] '. 'update disk usage',true,'cronlog');
$result = $class->getDiskUsage();
//$class->setLog('['.date("Y/m/d H:i").'] '. 'update disk usage done !',true,'cronlog');
print_r($result);

?>