<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('MeetingSchedule.class.php');
$meetingSchedule = createObjAndAddToCol(new MeetingSchedule());

$obj = $meetingSchedule;

$arrCriteria = array();
// array_push($arrCriteria, $obj->tableName . '.statuskey = 2');

include 'ajax-general.php';

if (isset($_GET)) {
    if (!isset($_GET) ||  empty($_GET['pkey']))   die;
    $pkey = $_GET['pkey'];

    switch ($_GET['action']) {
        case 'getMeeting':
            $result = $obj->getOnlineOfflineById($pkey);
            echo json_encode($result);
            break;
    }
}
die;
