<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';  
require_once '_global.php';  


includeClass(array('MeetingPoint.class.php'));

$meetingPoint = new MeetingPoint(); 

$obj = $meetingPoint;
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
if (isset($_GET) && !empty($_GET['action'])) {
		 
			$arrReturn = array();   
			switch ($_GET['action']) {
				case 'getLocationInformation':
							$arrReturn = $obj->searchData($obj->tableName.'.pkey',$_GET['pkey']); 
							echo json_encode($arrReturn);   
							break;
			}
	  
}

die;
  
?>