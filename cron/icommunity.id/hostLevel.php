<?php     

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../_include-cron.php';

includeClass(array('Customer.class.php','MeetingSchedule.class.php'));
 
$customer = new Customer();
$meetingSchedule = new MeetingSchedule();

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name'),
									  ' and '.$customer->tableName.'.hostlevelkey = 1 and  '.$customer->tableName.'.statuskey = 2'); // kalo dr pro, akan dihitung pas ikut meeting, kalo sudah master host gk perlu cek lg

foreach($rsCustomer as $row){
	//$meetingSchedule->setLog($row['name'],true,'host-cron');
	$meetingSchedule->updateHostLevelKey($row['pkey']); 
}

echo 'Host Done ! ';
	
?>