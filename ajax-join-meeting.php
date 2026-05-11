<?php
require_once '_config.php';
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('MeetingSchedule.class.php','Customer.class.php'));
$meetingSchedule = new MeetingSchedule();
$customer = new Customer();

if (isset($_POST) && !empty($_POST['action'])) {

	foreach ($_POST as $k => $v) {
		if (!is_array($v))
			$v = trim($v);
		$arr[$k] = $v;
	}

	$arrReturn = array();
	switch ($_POST['action']) {
		case 'join':
			$arr['hidBusinessKey']= $arr['selCategory'];  
			$arr['hidCustomerKey']= USERKEY;
			
			$arrReturn = $meetingSchedule->addDetail($arr);
			break;
	}
	echo json_encode($arrReturn);
	die;
}
