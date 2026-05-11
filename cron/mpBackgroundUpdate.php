<?php     
 

// gk bisa karena perlu domain
// require_once dirname(__FILE__).'/_include-cron.php';

set_include_path('/home/programstok/minerva/');   
require_once '_config.php'; 
require_once '_include-v2.php';  

includeClass(array('Marketplace.class.php'));

$marketplace = new Marketplace();
$marketplace->executeBackgroundJob();

echo 'Updated ! ';
	
?>