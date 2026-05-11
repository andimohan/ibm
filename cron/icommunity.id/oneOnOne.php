<?php     
// email reminder ikut ilc

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../_include-cron.php';
//require_once '../_include-cron.php'; // khusus development

includeClass(array('MeetingSchedule.class.php','Employee.class.php'));
 
$meetingSchedule = new MeetingSchedule(2);

// jam nya mepet pas 30 menit, coba lihat nanti selalu tepat waktu gk
// harus masukin criteria utk meeting ket, karena search data row gk ad criteria bawaan
$rs = $meetingSchedule->searchDataRow(array($meetingSchedule->tableName.'.pkey'), 
									  ' and '.$meetingSchedule->tableName.'.statuskey = 1 
									  	and '.$meetingSchedule->tableName.'.meetingtypekey = '.$meetingSchedule->meetingType.'
									    and '.$meetingSchedule->tableName.'.trdate - interval 2 hour = DATE_FORMAT(now(), \'%Y-%m-%d %H:%i\')'
									  );

foreach($rs as $row)
	$meetingSchedule->sendReminderOneOnOneEmail($row['pkey']);

echo 'One on One Sent ! ';
	
?>