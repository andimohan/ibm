<?php     
require_once '../_config.php'; 
require_once '_include.php';
 
//$class->setLog('['.date("Y/m/d H:i").'] '.' cron called',true,'cronlog');
$class->runCronJob();
echo 'done';
?>