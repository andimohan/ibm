<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  


includeClass(array('Customer.class.php','MeetingSchedule.class.php','MembershipLevel.class.php','PaymentType.class.php'));

$customer = new Customer();
$meetingSchedule = new MeetingSchedule(1); // yg host saja
$membershipLevel = new MembershipLevel();
$customerFeatures = new CustomerFeatures();
$paymetType = new PaymentType();

$criteria  ='';

// testing
//$testUser = array(8014,8091,8092);
//if(!in_array(USERKEY,$testUser))
//	$criteria .= ' and hostkey not in ('.$class->oDbCon->paramString($testUser ,',').') ';

$rsUser = $customer->getDataRowById(USERKEY); 
if(!empty($rsUser)) $rsUser = $rsUser[0];

// utamain GET, karena dr sub menu / paging
if(!isset($_POST['hidMeetingType'])){
	$onlineOffLine = (isset($_GET['online'])  && strtolower($_GET['online']) == 'offline' ) ? 2 : 1;
	$_POST['hidMeetingType']  = $onlineOffLine;
}

$rsPaymentType  = $paymetType->searchDataRow(array($paymetType->tableName.'.pkey',$paymetType->tableName.'.name'), ' and '.$paymetType->tableName.'.statuskey = 1');
$rsPaymentType  = $paymetType->updateContentLang($rsPaymentType);
$arrPaymentTypeByPkey = array_column($rsPaymentType,'name','pkey');


//pastikan cuma 1 atau 2
$_POST['hidMeetingType'] = ($_POST['hidMeetingType'] == 1) ? 1 : 2;
	
$rsFeatureDetail = $customerFeatures->getFeaturesQuota(USERKEY,array('funckey' => ($_POST['hidMeetingType'] == 1) ? 'onlineMeeting' : 'offlineMeeting'));

$rsMeeting = array();
$totalPages = 0;

// karena online tetep boleh lihat

// khusus offline
if( !empty(USERKEY) && $_POST['hidMeetingType'] == 2 && !empty($rsFeatureDetail) && $rsFeatureDetail[0]['quotaused'] >= $rsFeatureDetail[0]['quota']  ){ 
	$exceedQuota = true;
}else{
	$exceedQuota = false;

	$pageIndex =  ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
	$arrTwigVar ['pageIndex'] =  $pageIndex;

	$totalrowsperpage = $class->loadSetting('productTotalItemPerPage');

	$now = $pageIndex * $totalrowsperpage;
	$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

	$criteria .= ' and '.$meetingSchedule->tableName.'.trdate > DATE_SUB(NOW(), INTERVAL 2 HOUR) 
				   and '.$meetingSchedule->tableName.'.statuskey = 1
				   and '.$meetingSchedule->tableName.'.meetingonlineoffline = '. $class->oDbCon->paramString($_POST['hidMeetingType']);

	if (isset($_POST) && !empty($_POST['trStartDate'])) { 
		$criteria .= ' AND '.$meetingSchedule->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59');
	}else{
		$_POST['trStartDate'] = date('d / m / Y'); 
		$_POST['trEndDate'] = date('d / m / Y', strtotime('+1 year'));  
	}

	$searchCriteria = array();
	if (isset($_POST) && !empty($_POST['search'])) {
		array_push($searchCriteria, $meetingSchedule->tableName . '.name LIKE ' . $class->oDbCon->paramString('%' . $_POST['search'] . '%'));
		array_push($searchCriteria, $tableCustomer->tableName . '.name LIKE ' . $class->oDbCon->paramString('%' . $_POST['search'] . '%'));
	}

	if(!empty($searchCriteria))
		  $criteria .= ' AND ('.implode(' OR ', $searchCriteria).') ';

	$orderBy = ' order by '.$meetingSchedule->tableName.'.trdate asc, '.$meetingSchedule->tableName.'.pkey desc'; 
	$rsMeeting = $meetingSchedule->searchData('','',true,$criteria,$orderBy,$limit );

	$rsParticipantInformation = $meetingSchedule->getParticipantInformation(array_column($rsMeeting,'pkey'));
		 
	foreach($rsMeeting as $key => $row){  
        
        // kalo ada login, format ke GMT user nya
        if(!empty($rsUser)){
            $rsMeeting[$key]['trdate'] = $class->convertToLocalTimeZone($row['trdate'],$row['gmt'],$rsUser['gmt']);
            $rsMeeting[$key]['gmt'] = $rsUser['gmt'];
        }
        
		$meetingTime = $class->formatDBDate($row['trdate'],'d / m / Y H:i');
		
		$rsMeeting[$key]['meetingpointaddress']= str_replace(chr(13),'<br>',$rsMeeting[$key]['meetingpointaddress']); 
		$rsMeeting[$key]['hostphotohash']= getPHPThumbHash($rsMeeting[$key]['hostphoto']);  
		
		$arrParticipantInformation = $rsParticipantInformation[$rsMeeting[$key]['pkey']]; 
		$rsMeeting[$key]['participantInformation'] = $arrParticipantInformation;
		
		$rsMeeting[$key]['beforemeeting'] = $meetingSchedule->checkBeforeTime($meetingTime);
	 
		$rsMeeting[$key]['button'] = 0; 
		if ($rsMeeting[$key]['hostkey'] == USERKEY) $rsMeeting[$key]['button']= 1;
		else if(in_array(USERKEY, $arrParticipantInformation['participantkey'])) 	$rsMeeting[$key]['button']= 2;
        
        $rsMeeting[$key]['paymenttypename'] = $arrPaymentTypeByPkey[$rsMeeting[$key]['paymenttypekey']];
	}

	$totalPages = ceil( $meetingSchedule->getTotalRows($criteria) / $totalrowsperpage);

}


$arrTwigVar ['totalPages'] =  $totalPages;

$arrTwigVar ['rsUser'] =  $rsUser;
$arrTwigVar ['rsMeeting'] =  $rsMeeting;  
$arrTwigVar ['exceedQuota'] =  $exceedQuota; 
$arrTwigVar ['inputHidMeetingType'] =  $class->inputHidden('hidMeetingType'); 
$arrTwigVar ['inputSearch'] =  $class->inputText('search');
$arrTwigVar ['inputSearchPlaceholder'] =  $class->inputText('search', array( 'etc' => 'placeholder="'.$class->lang['searchMeeting'].' ..."'));
$arrTwigVar ['inputStartDate'] =  $class->inputDate('trStartDate');
$arrTwigVar ['inputEndDate'] =  $class->inputDate('trEndDate');
$arrTwigVar ['btnSearch'] =   $class->inputSubmit('btnSearch',$class->lang['search']);
$arrTwigVar ['meetingType'] = $_POST['hidMeetingType'];
	
$arrTwigVar ['btnJoin'] =  $class->inputSubmit('btnJoin',$class->lang['joinMeeting']);
	
if($_POST['hidMeetingType'] == 2)
	$arrTwigVar ['selectedOffline'] = 'selected'; 
else
	$arrTwigVar ['selectedOnline'] = 'selected';

	
echo $twig->render('find-meeting.html', $arrTwigVar);

?>