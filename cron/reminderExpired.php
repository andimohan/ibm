<?php     
// email reminder ikut ilc

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/_include-cron.php';
//require_once '../_include-cron.php';

includeClass(array('Customer.class.php','MembershipSubscription.class.php','Employee.class.php'));


$arrInterval = array('30', '14', '3', '2', '1', '0', '-1');

$customer = new Customer();
$membershipSubscription = new MembershipSubscription();



foreach($arrInterval as $value){
   $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.expdate',$customer->tableName.'.membershiplevel',$customer->tableName.'.langkey'), 
									  ' and '.$customer->tableName.'.statuskey = 2 
									    and '.$customer->tableName.'.membershiplevel in (2,3)
										and date(now()) + interval '.$value.' day = date('.$customer->tableName.'.expdate) 
										'
									  );
    
    $membershipSubscription->sendReminderWillExpiredEmail($rsCustomer,$value);

}
//echo 'Reminder Sent ! ';
	
?>