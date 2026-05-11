<?php
require_once '_config.php';
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('MembershipSubscription.class.php','Warehouse.class.php','TermOfPayment.class.php'));
$membershipSubscription = new MembershipSubscription(); 
$warehouse = new Warehouse(); 
$termOfPayment = new TermOfPayment();

if (isset($_POST) && !empty($_POST['action'])) {

	foreach ($_POST as $k => $v) {
		if (!is_array($v))
			$v = trim($v);
		$arr[$k] = $v;
	}

	$arrReturn = array();
	switch ($_POST['action']) {
		case 'add':
			
			if(empty(USERKEY)) die; 
			
            $arr['code'] = 'xxxx';
            $arr['trDate'] = date('d / m / Y');
			$arr['selWarehouseKey'] = $warehouse->getDefaultData();
			$arr['hidCustomerKey'] = USERKEY;
			$arr['selStatus'] = 1;
			$arr['selMembershipLevel'] = $arr['selMembership'];
			
			$rsTOP = $termOfPayment->searchData($termOfPayment->tableName.'.statuskey',1,true,' order by pkey asc', 'limit 1'); 
			$arr['selTermOfPaymentKey'] =  $rsTOP[0]['pkey']; 
 
			$arrReturn = $membershipSubscription->addData($arr);
			
			break;
	}
    
	echo json_encode($arrReturn);
	die;
}
