<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';   

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('MeetingPoint.class.php', 'MeetingSchedule.class.php','OnlineChannel.class.php', 'City.class.php', 'CityCategory.class.php','PaymentType.class.php'));

$meetingSchedule = new MeetingSchedule(1);
$meetingPoint = new MeetingPoint(); 
$onlineChannel = new OnlineChannel();
$city = new City();
$cityCategory = new CityCategory();
$paymetType = new PaymentType();

//$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
//$arrTwigVar['pageIndex'] =  $pageIndex;
//$totalrowsperpage = $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu
//$now = $pageIndex * $totalrowsperpage;
//$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria = ' and '.$meetingSchedule->tableName.'.trdate > DATE_SUB(NOW(), INTERVAL 1 HOUR)
			  and ' . $meetingSchedule->tableName . '.statuskey =1';
$rsMeetingSchedule = $meetingSchedule->getAllMeeting($class->oDbCon->paramString(USERKEY), $criteria); // $limit utk meeting sementara hilangkan paging, karena akan bentrok, dan harusnya gk akan byk jg onggoing meeting
//$totalPages = ceil(count($meetingSchedule->getAllMeeting($class->oDbCon->paramString(USERKEY),$criteria)) / $totalrowsperpage);

//$totalPages = 0; // khusus hsitori
	
$rsParticipantInformation = $meetingSchedule->getParticipantInformation(array_column($rsMeetingSchedule,'pkey'), array('detail'));
	
$rsPaymentType  = $paymetType->searchDataRow(array($paymetType->tableName.'.pkey',$paymetType->tableName.'.name'), ' and '.$paymetType->tableName.'.statuskey = 1');
$rsPaymentType  = $paymetType->updateContentLang($rsPaymentType);
$arrPaymentType = $class->generateComboboxOpt(array('data' => $rsPaymentType, 'label' => 'name'));
$arrPaymentTypeByPkey = array_column($rsPaymentType,'name','pkey');

$now = date('d / m / Y H:i');
 

$_POST['selTimeZone'] = $LOGIN_USER['gmt']; 

for ($i = 0; $i < count($rsMeetingSchedule); $i++) {
	$meetingkey = $rsMeetingSchedule[$i]['pkey']; 
	$rsMeetingSchedule[$i]['trdate'] = $class->convertToLocalTimeZone($rsMeetingSchedule[$i]['trdate'],$rsMeetingSchedule[$i]['gmt'] ,$LOGIN_USER['gmt']);
    
    $meetingTime = $class->formatDBDate($rsMeetingSchedule[$i]['trdate'],'d / m / Y H:i');
		
    $rsMeetingSchedule[$i]['hostphotohash'] = getPHPThumbHash($rsMeetingSchedule[$i]['hostphoto']);
   
	$arrParticipantInformation = $rsParticipantInformation[$meetingkey];  
    $arrDetailParticipants  = $arrParticipantInformation['detail'];
		
	$rsMeetingSchedule[$i]['participantInformation'] = $arrParticipantInformation;
	 
	$rsMeetingSchedule[$i]['inremindertime'] = $meetingSchedule->inReminderTime($meetingTime);
	$rsMeetingSchedule[$i]['inmeetingtime'] = $meetingSchedule->inMeetingTime($meetingTime); 
	$rsMeetingSchedule[$i]['beforemeeting'] = $meetingSchedule->checkBeforeTime($meetingTime);
	
	$arrCustomer = array_column($arrDetailParticipants,null,'customerkey'); 
	$arrCustomer = (isset($arrCustomer[USERKEY])) ? $arrCustomer[USERKEY] : array();
	$rsMeetingSchedule[$i]['ischeckin'] = isset($arrCustomer['ischeckin']) ? $arrCustomer['ischeckin'] : 0; 
	  
    $rsMeetingSchedule[$i]['paymenttypename'] =  $arrPaymentTypeByPkey[$rsMeetingSchedule[$i]['paymenttypekey']]; 

}


// history  
$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage = $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderby = ' order by '. $meetingSchedule->tableName.'.trdate desc';

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria = ' and '.$meetingSchedule->tableName.'.trdate <= DATE_SUB(NOW(), INTERVAL 1 HOUR)
			  and '.$meetingSchedule->tableName.'.hostkey = '.$class->oDbCon->paramString(USERKEY).'
			  and ' . $meetingSchedule->tableName . '.statuskey in (1,2,3)';

$rsMeetingHistory = $meetingSchedule->searchData('','',true,$criteria,$orderby,$limit);

$totalPages = ceil( $meetingSchedule->getTotalRows($criteria) / $totalrowsperpage);  
$rsParticipantInformation = $meetingSchedule->getParticipantInformation(array_column($rsMeetingHistory,'pkey'), array('detail'));
for ($i = 0; $i < count($rsMeetingHistory); $i++) {
	$meetingkey = $rsMeetingHistory[$i]['pkey'];  
		
    $rsMeetingHistory[$i]['trdate'] = $class->convertToLocalTimeZone($rsMeetingHistory[$i]['trdate'],$rsMeetingHistory[$i]['gmt'] ,$LOGIN_USER['gmt']);
    
    $rsMeetingHistory[$i]['hostphotohash'] = getPHPThumbHash($rsMeetingHistory[$i]['hostphoto']);
   
	$arrParticipantInformation = $rsParticipantInformation[$meetingkey];  
    $arrDetailParticipants  = $arrParticipantInformation['detail'];
		
	$rsMeetingHistory[$i]['participantInformation'] = $arrParticipantInformation;
  
	$arrCustomer = array_column($arrDetailParticipants,null,'customerkey'); 
	$arrCustomer = (isset($arrCustomer[USERKEY])) ? $arrCustomer[USERKEY] : array();
	$rsMeetingHistory[$i]['ischeckin'] = isset($arrCustomer['ischeckin']) ? $arrCustomer['ischeckin'] : 0; 
	 
    $rsMeetingHistory[$i]['paymenttypename'] =  $arrPaymentTypeByPkey[$rsMeetingHistory[$i]['paymenttypekey']];
}
 

// twig

$arrMeetingSchedule = $meetingSchedule->getOnlineOffline(); 

$arrMeeting = $class->generateComboboxOpt(array('data' => $arrMeetingSchedule));
$arrTwigVar ['inputSelMeeting'] =  $class->inputSelect('selOnlineOffline',$arrMeeting); 
$arrTwigVar['rsMeeting'] =  $rsMeetingSchedule; 
$arrTwigVar['rsMeetingHistory'] =  $rsMeetingHistory; 
	
$arrLanguage = $class->generateComboboxOpt(array('data' => $meetingSchedule->getLanguage(), 'label' => 'language'));
$arrTwigVar ['inputSelLanguage'] =  $class->inputSelect('selLanguage',$arrLanguage); 

$arrOnlineChannel = $onlineChannel->generateComboboxOpt(null,array('criteria' =>' and ('.$onlineChannel->tableName.'.statuskey=1)')); 
$arrTwigVar ['inputSelChannel'] =  $class->inputSelect('selOnlineChannel',$arrOnlineChannel); 

$_POST['trDate'] = date('d / m / Y', strtotime('+1 day'));
$arrTwigVar ['inputDate'] =  $class->inputDate('trDate',array('etc' => 'style="text-align:center" min-days=0')); 
$arrTwigVar ['inputSelHour'] =  $class->inputSelect('selHour',$class->generateHourSelectBox()); 


$arrTwigVar ['inputSelPaymentType'] =  $class->inputSelect('selPaymentType',$arrPaymentType); 
$arrTwigVar ['inputMeetingLink'] =  $class->inputTextArea('meetingLink', array('etc' => 'style="height:10em"')); 
$arrTwigVar ['inputTopic'] =  $class->inputText('name');  
$arrTwigVar ['inputLocation'] = $class->inputAutoComplete(array( 
                                            'element' => array(
                                                'value' => 'meetingPoint',
                                                'key' => 'hidMeetingPointKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-meeting-point.php',
                                                'data' => array(
                                                    'action' => 'searchData'
                                                )
                                            ), 
                                        ));


$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array('readonly' =>true, 'etc' => 'style="height:10em"')); 
$arrTwigVar ['inputCity'] =  $class->inputText('city', array('readonly' =>true)); 
$arrTwigVar ['inputSelGMT'] =  $class->inputSelect('selTimeZone', $class->getGMT()); 

$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$arrTwigVar['btnDetail'] =   $class->inputSubmit('btnDetail', $class->lang['showDetail']); 
$arrTwigVar['btnCancel'] =   $class->inputSubmit('btnCancel', $class->lang['cancel']); 
$arrTwigVar['btnJoin'] =   $class->inputSubmit('btnJoin', $class->lang['present']); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 

$arrTwigVar['totalPages'] =  $totalPages; 
array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
	

echo $twig->render('host-meeting.html', $arrTwigVar);

?>