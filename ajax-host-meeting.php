<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
	 
includeClass(array('MeetingSchedule.class.php'));

$meetingType = (!empty($_POST['hidMeetingType'])) ? $_POST['hidMeetingType'] : 1;

if(!in_array($meetingType,array(1,2))) $meetingType = 1; // utk jaga2

$meetingSchedule = new MeetingSchedule($meetingType);

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
				
					$arr['code'] = 'XXXXX';
					$arr['hidHostKey'] = USERKEY;
					$arr['createdBy'] = 0;
					$arr['selStatus'] = 1;  
					$arr['fromFE'] = 1;
					
					$arrReturn = $meetingSchedule->addData($arr);

					break;

            case 'cancel':
					if(empty(USERKEY)) die;
				
                    $arr['fromFE'] = 1;
                    $arr['hidCustomerKey'] = USERKEY;

                    $arrReturn = $meetingSchedule->cancelMeeting($arr);
                    break;
				
            case 'checkIn':
					if(empty(USERKEY)) die;
				
                    $arr['fromFE'] = 1;
                    $arr['hidCustomerKey'] = USERKEY;

                    $arrReturn = $meetingSchedule->checkInMeeting($arr);
                    break;
				
				
            case 'cancelCheckIn':
					if(empty(USERKEY)) die;
				
                    $arr['fromFE'] = 1;
                    $arr['hidCustomerKey'] = USERKEY;
 
                    $arrReturn = $meetingSchedule->cancelCheckInMeeting($arr);
                    break;
				
			case 'reminder':
					if(empty(USERKEY) || empty($_POST['hidMeetingKey']) ) die; 
                    $arrReturn = $meetingSchedule->sendReminder($_POST['hidMeetingKey'], USERKEY);
                    break;
					
		}; 

		echo json_encode($arrReturn);  
		die;  
}

	 
?>