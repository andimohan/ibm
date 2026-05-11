<?php     
// email reminder ikut ilc

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../_include-cron.php';
//require_once '../_include-cron.php';

includeClass(array('Customer.class.php','Event.class.php','Employee.class.php'));
 
$customer = new Customer();
$event  = new Event();

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.langkey'), 
									  ' and '.$customer->tableName.'.statuskey = 2'
									  );
//
//$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobilecode',$customer->tableName.'.mobile',$customer->tableName.'.langkey'), 
//									  ' and '.$customer->tableName.'.pkey = 8014'
//									  );

$rsEvent = $event->searchDataRow(array($event->tableName.'.pkey',$event->tableName.'.speakers',$event->tableName.'.title',$event->tableName.'.eventdatefrom'), 
									  ' and '.$event->tableName.'.statuskey = 1
									    and '.$event->tableName.'.eventdatefrom > now()',
								 	  ' order by '.$event->tableName.'.eventdatefrom asc limit 1'
									  );
$lang = new Lang();
 
    
foreach($rsCustomer as $row){ 
    $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$event->oDbCon->paramString($row['langkey'])
                              );
    
    // perlu konversi ke array 2 dimensi dulu
    $rsEventInLang = $event->updateContentLang(array($rsEvent[0]),$rsLang[0]['code']);
	$event->sendEventEmail($row,$rsEventInLang[0],$rsLang[0]['code']);
}

echo 'ILC Sent ! ';
	
?>