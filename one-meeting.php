<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('MeetingSchedule.class.php','MeetingPoint.class.php','OnlineChannel.class.php','PaymentType.class.php'));
$meetingSchedule = new MeetingSchedule(2);
$onlineChannel = new OnlineChannel();
$meetingPoint = new MeetingPoint();
$customer = new Customer();
$paymetType = new PaymentType();
	
$rsPaymentType  = $paymetType->searchDataRow(array($paymetType->tableName.'.pkey',$paymetType->tableName.'.name'), ' and '.$paymetType->tableName.'.statuskey = 1');
$rsPaymentType  = $paymetType->updateContentLang($rsPaymentType);
$arrPaymentType = $class->generateComboboxOpt(array('data' => $rsPaymentType, 'label' => 'name'));
$arrPaymentTypeByPkey = array_column($rsPaymentType,'name','pkey');


$_POST['selTimeZone'] = $LOGIN_USER['gmt']; 

//$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
//$arrTwigVar['pageIndex'] =  $pageIndex; 
//$totalrowsperpage = $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu 
//$orderBy = ' order by '.$meetingSchedule->tableName.'.trdate asc '; 
//$now = $pageIndex * $totalrowsperpage;
//$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$criteria = ' and '.$meetingSchedule->tableName.'.trdate > DATE_SUB(NOW(), INTERVAL 1 HOUR)
			 and ' . $meetingSchedule->tableName . '.statuskey = 1
			 and ( ' . $meetingSchedule->tableName . '.hostkey=' . $class->oDbCon->paramString(USERKEY) . ' OR ' . $meetingSchedule->tableName . '.partnerkey =' . $class->oDbCon->paramString(USERKEY) .' )';

$rsMeetingSchedule = $meetingSchedule->searchData('','',true,$criteria);

$rsPartnerCol = $customer->searchData('','',true,' and ' .$customer->tableName.'.pkey in ('. $class->oDbCon->paramString( array_column($rsMeetingSchedule,'partnerkey') ,',') .')');
$rsPartnerCol = array_column($rsPartnerCol,null,'pkey');

//$totalPages = ceil( $meetingSchedule->getTotalRows($criteria) / $totalrowsperpage);

for ($i = 0; $i < count($rsMeetingSchedule); $i++) {
	 
    $rsMeetingSchedule[$i]['trdate'] = $class->convertToLocalTimeZone($rsMeetingSchedule[$i]['trdate'],$rsMeetingSchedule[$i]['gmt'] ,$LOGIN_USER['gmt']);
    
	$meetingTime = $class->formatDBDate($rsMeetingSchedule[$i]['trdate'],'d / m / Y H:i');
    
	
    $_POST['hidMeetingKey'] = $rsMeetingSchedule[$i]['pkey'];
	$rsMeetingSchedule[$i]['inputHidMeetingKey'] = $class->inputHidden('hidMeetingKey'); 
    
	// harus didetail karena nyampur antara history user yg invite atau diinvite
	if($rsMeetingSchedule[$i]['hostkey'] == USERKEY){ 
		$partnerkey = $rsMeetingSchedule[$i]['partnerkey'];
		$rsMeetingSchedule[$i]['partnerphotohash'] = getPHPThumbHash($rsMeetingSchedule[$i]['partnerphoto']); 
		$rsMeetingSchedule[$i]['companyname'] = $rsPartnerCol[$partnerkey]['companyname'];
		$rsMeetingSchedule[$i]['jobpositionname'] = $rsPartnerCol[$partnerkey]['jobpositionname'];
	}else{   
		$rsMeetingSchedule[$i]['partnerkey'] = $rsMeetingSchedule[$i]['hostkey']; 
		$rsMeetingSchedule[$i]['partnerphoto'] = $rsMeetingSchedule[$i]['hostphoto']; 
		$rsMeetingSchedule[$i]['partnerphotohash'] = getPHPThumbHash($rsMeetingSchedule[$i]['hostphoto']); 
		$rsMeetingSchedule[$i]['partnercode'] =  $rsMeetingSchedule[$i]['customercode'];
		$rsMeetingSchedule[$i]['partnername'] =  $rsMeetingSchedule[$i]['customername'];
	}
	
	
	$rsMeetingSchedule[$i]['inmeetingtime'] = $meetingSchedule->inMeetingTime($meetingTime);
	
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
			  and ( ' . $meetingSchedule->tableName . '.hostkey=' . $class->oDbCon->paramString(USERKEY) . ' OR ' . $meetingSchedule->tableName . '.partnerkey =' . $class->oDbCon->paramString(USERKEY) .' )
			  and ' . $meetingSchedule->tableName . '.statuskey in (1,2,3)';

$rsMeetingHistory = $meetingSchedule->searchData('','',true,$criteria,$orderby,$limit);


$rsPartnerCol = $customer->searchData('','',true,' and ' .$customer->tableName.'.pkey in ('. $class->oDbCon->paramString( array_column($rsMeetingHistory,'partnerkey') ,',') .')');
$rsPartnerCol = array_column($rsPartnerCol,null,'pkey');

$totalPages = ceil( $meetingSchedule->getTotalRows($criteria) / $totalrowsperpage);   
for ($i = 0; $i < count($rsMeetingHistory); $i++) {
	
    $rsMeetingHistory[$i]['trdate'] = $class->convertToLocalTimeZone($rsMeetingHistory[$i]['trdate'],$rsMeetingHistory[$i]['gmt'] ,$LOGIN_USER['gmt']);
    
	$_POST['hidMeetingKey'] = $rsMeetingHistory[$i]['pkey'];
	$rsMeetingHistory[$i]['inputHidMeetingKey'] = $class->inputHidden('hidMeetingKey'); 
	
	// harus didetail karena nyampur antara history user yg invite atau diinvite
	if($rsMeetingHistory[$i]['hostkey'] == USERKEY){ 
		$partnerkey = $rsMeetingHistory[$i]['partnerkey'];
		$rsMeetingHistory[$i]['partnerphotohash'] = getPHPThumbHash($rsMeetingHistory[$i]['partnerphoto']); 
		$rsMeetingHistory[$i]['companyname'] = $rsPartnerCol[$partnerkey]['companyname'];
		$rsMeetingHistory[$i]['jobpositionname'] = $rsPartnerCol[$partnerkey]['jobpositionname'];
	}else{ 
		$rsMeetingHistory[$i]['partnerkey'] = $rsMeetingHistory[$i]['hostkey']; 
		$rsMeetingHistory[$i]['partnerphoto'] = $rsMeetingHistory[$i]['hostphoto']; 
		$rsMeetingHistory[$i]['partnerphotohash'] = getPHPThumbHash($rsMeetingHistory[$i]['hostphoto']); 
		$rsMeetingHistory[$i]['partnercode'] =  $rsMeetingHistory[$i]['customercode'];
		$rsMeetingHistory[$i]['partnername'] =  $rsMeetingHistory[$i]['customername'];
	}

    $rsMeetingHistory[$i]['paymenttypename'] =  $arrPaymentTypeByPkey[$rsMeetingHistory[$i]['paymenttypekey']];
}

$arrTwigVar['rsMeeting'] =  $rsMeetingSchedule;
$arrTwigVar['rsMeetingHistory'] =  $rsMeetingHistory; 

// twig
$arrMeetingPoint = $class->generateComboboxOpt(array('data' => $meetingPoint->searchDataRow(array($meetingPoint->tableName.'.pkey',$meetingPoint->tableName.'.name'), ' and '.$meetingPoint->tableName.'.statuskey = 1'), 'label' => 'name'));
$arrOnlineChannel = $onlineChannel->generateComboboxOpt(null,array('criteria' =>' and ('.$onlineChannel->tableName.'.statuskey=1)')); 
$arrTwigVar ['inputSelChannel'] =  $class->inputSelect('selOnlineChannel',$arrOnlineChannel); 

$_POST['trDate'] = date('d / m / Y', strtotime('+1 day'));

$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$_POST['hidMeetingType'] ='2';  
$arrTwigVar ['inputHidMeetingType'] =  $class->inputHidden('hidMeetingType'); 

$arrMeeting = $class->generateComboboxOpt(array('data' => $meetingSchedule->getOnlineOffline(),'label' => 'name')); 
$arrTwigVar ['inputSelMeeting'] =  $class->inputSelect('selOnlineOffline',$arrMeeting); 

$arrLanguage = $class->generateComboboxOpt(array('data' => $meetingSchedule->getLanguage(), 'label' => 'language'));
$arrTwigVar ['inputSelLanguage'] =  $class->inputSelect('selLanguage',$arrLanguage); 

$arrTwigVar ['inputSelPaymentType'] =  $class->inputSelect('selPaymentType',$arrPaymentType); 
$arrTwigVar ['inputSelGMT'] =  $class->inputSelect('selTimeZone', $class->getGMT()); 

//$arrTwigVar ['inputMeetingPoint']  = $class->inputAutoComplete(
//                                        array( 
//                                            'element' => array(
//                                                'value' => 'meetingPoint',
//                                                'key' => 'hidMeetingPointKey'
//                                            ),
//                                            'source' => array(
//                                                'url' => 'ajax-meeting-point.php',
//                                                'data' => array('action' => 'searchData')
//                                            )
//                                        )
//                                    );; 
$arrTwigVar ['inputMember']  = $class->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'partnerName',
                                                'key' => 'hidPartnerKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array('action' => 'searchData', 'membershiplevel' => 3, 'searchField' => 'code,name')
                                            )
                                        )
                                    );

$arrTwigVar ['inputDate'] =  $class->inputDate('trDate',array('etc' => 'style="text-align:center" min-days=0')); 
$arrTwigVar ['inputSelHour'] =  $class->inputSelect('selHour',$class->generateHourSelectBox()); 

$arrTwigVar ['inputLinkMeeting'] =  $class->inputTextArea('meetingLink', array('etc' => 'style="height:10em"')); 
$arrTwigVar ['totalPages'] =  $totalPages;
$arrTwigVar ['inputTopic'] =  $class->inputText('name'); 
$arrTwigVar ['inputMeetingPoint'] =  $class->inputText('meetingPoint'); 
$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array('etc' => 'style="height:10em"')); 

$arrTwigVar ['inputCity']  = $class->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'cityName',
                                                'key' => 'hidCityKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-city.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );; 

  
$arrTwigVar ['btnCancel'] =   $class->inputSubmit('btnCancel', $class->lang['cancel']); 
$arrTwigVar ['btnJoin'] =   $class->inputSubmit('btnJoin', $class->lang['confirm']); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 

array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 

echo $twig->render('one-meeting.html', $arrTwigVar);

?>
