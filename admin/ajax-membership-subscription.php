<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('MembershipSubscription.class.php'));

$membershipSubscription = new MembershipSubscription();
$obj = $membershipSubscription;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 

if (isset($_GET) && !empty($_GET['action'])) {
          
	switch ($_GET['action']){  
		case 'getMembershipLevel' :    
			$membershipLevel = new MembershipLevel();
			$membershipLevelKey = (isset($_GET['membershipLevelKey']) && !empty($_GET['membershipLevelKey'])) ? $_GET['membershipLevelKey'] : 0;
			$result = $membershipLevel->getDataRowById($membershipLevelKey); 
			echo json_encode($result); 
			break;  
	}

} 

die;
  
?>