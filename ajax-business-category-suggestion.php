<?php
require_once '_config.php';
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('BusinessCategorySuggestion.class.php'));
$businessCategorySuggestion = new BusinessCategorySuggestion();


if (isset($_POST) && !empty($_POST['action'])) {

	foreach ($_POST as $k => $v) {
		if (!is_array($v))
			$v = trim($v);

		$arr[$k] = $v;
	}

	$arrReturn = array();

	switch ($_POST['action']) {
		case 'add':
			$arr['code'] = 'xxxx';
			$arr['selStatus'] = 1;
			$arr['fromFE'] = 1;
			$arr['hidCustomerKey'] = USERKEY;

			$arrReturn = $businessCategorySuggestion->addData($arr);
			break;
	}
	echo json_encode($arrReturn);
	die;
}
