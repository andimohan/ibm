<?php     

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/_include-cron.php';

includeClass(array('Customer.class.php'));
 
$customer = new Customer(); 
$customer->updateExpiredMemberStatus();
 
echo 'Exp Member Done ! ';
	
?>