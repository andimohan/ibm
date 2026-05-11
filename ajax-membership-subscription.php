<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
	 
includeClass(array('MembershipSubscription.class.php'));
$membershipSubscription = new MembershipSubscription(); 

if (isset($_POST) && !empty($_POST['action'])) {

		foreach ($_POST as $k => $v) { 
			if (!is_array($v))
				 $v = trim($v);  

			$arr[$k] = $v;     
		}  

		$arrReturn = array();  

		switch ($_POST['action']) {

			case 'get-pending-subscription' :

					if(empty($_POST['userkey'])) die; 
					$arrReturn = $membershipSubscription->getPendingSubscription($_POST['userkey']);  
					break;

		}; 

		echo json_encode($arrReturn);  
		die;  
}

	 
?>