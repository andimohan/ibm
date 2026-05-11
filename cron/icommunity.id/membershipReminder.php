<?php     
// email reminder ikut ilc

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../_include-cron.php';
//require_once '../_include-cron.php';

includeClass(array('Customer.class.php','MembershipSubscription.class.php','Employee.class.php'));

$customer = new Customer();
$membershipSubscription = new MembershipSubscription();

// cari yg 14 hr
$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.membershiplevel',$customer->tableName.'.langkey'), 
									  ' and '.$customer->tableName.'.statuskey = 2 
									    and '.$customer->tableName.'.membershiplevel < 3 
										and date('.$customer->tableName.'.activationdate) + interval 14 day = date(now()) 
										'
									  );
 
$membershipSubscription->sendReminderDaysEmail($rsCustomer,14);


// cari yg 30 hr
$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.membershiplevel',$customer->tableName.'.langkey'), 
									  ' and '.$customer->tableName.'.statuskey = 2 
									    and '.$customer->tableName.'.membershiplevel < 3 
										and date('.$customer->tableName.'.activationdate) + interval 1 month = date(now()) 
										'
									  );

$membershipSubscription->sendReminderDaysEmail($rsCustomer,30);

echo 'Reminder Sent ! ';
	
?>