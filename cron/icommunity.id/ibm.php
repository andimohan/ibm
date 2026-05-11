<?php     
// email reminder ikut ilc

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../_include-cron.php';
//require_once '../_include-cron.php';

includeClass(array('Customer.class.php','MeetingSchedule.class.php','Employee.class.php','GiveOpportunity.class.php'));

$customer = new Customer();
$meetingSchedule = new MeetingSchedule();
$giveOpportunity = new GiveOpportunity(); 
	
$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.langkey'), 
									  ' and '.$customer->tableName.'.statuskey = 2'
									  );

//
//$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.langkey'), 
//									  ' and '.$customer->tableName.'.pkey = 8014 '
//									  );


$rsCounter = $giveOpportunity->countSummary();
$indexCounter = array();  
$indexCounter['businessRefer'] = $rsCounter['businessRefer'] + 10;
$indexCounter['transactionAmount'] = $rsCounter['transactionAmount']; //($rsCounter['transactionAmount'] + 100000000) / 1000000; 

foreach($rsCustomer as $row){ 
	$meetingSchedule->sendIBMEmail($row,$indexCounter);
}

echo 'IBM Sent ! ';
	
?>