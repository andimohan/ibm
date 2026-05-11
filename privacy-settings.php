<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

$_POST['selEmailPrivacyKey'] = $LOGIN_USER['emailprivacykey'];
$_POST['selMobilePrivacyKey'] = $LOGIN_USER['mobileprivacykey'];

$rsMembership = array();
if($class->isActiveModule('MembershipLevel')){  
	includeClass(array('MembershipLevel.class.php'));
	$membershipLevel = new MembershipLevel();
	$rsMembership = $membershipLevel->getDataRowById($LOGIN_USER['membershiplevel']);
	
	// nanti kalo user free boleh lihat baru diudpate where nya
 	$rsMembershipLv = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',$membershipLevel->tableName.'.name'),
                                             ' and '.$membershipLevel->tableName.'.statuskey = 1 
											   and '.$membershipLevel->tableName.'.pkey > 1'
											);
            
//    $arrRoot = array();
//    $arrRoot[0]['name'] = $class->lang['public'];
//    $arrRoot[0]['pkey'] = 0;
//    $rsMembershipLv = array_merge($arrRoot,$rsMembershipLv);
	
    $arrNone = array();
    $arrNone[999]['name'] = $class->lang['private'];
    $arrNone[999]['pkey'] = 999;
    $rsMembershipLv = array_merge($rsMembershipLv,$arrNone);
    
    $arrMembershipLv = $class->generateComboboxOpt(array('data' => $rsMembershipLv));
    $arrTwigVar ['inputSelEmailPrivacy'] =  $class->inputSelect('selEmailPrivacyKey',$arrMembershipLv); 
    $arrTwigVar ['inputSelMobilePrivacy'] =  $class->inputSelect('selMobilePrivacyKey',$arrMembershipLv); 
}

 
$_POST['hidId'] = $rs[0]['pkey'];  
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId');
$arrTwigVar ['rsMembershipLv'] =  $rsMembershipLv;

$_POST['action'] ='privacy-settings';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$_POST['hidModifiedOn'] =  $rs[0]['modifiedon']; 
$arrTwigVar['hidModifiedOn'] = $class->inputHidden('hidModifiedOn'); 

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 
array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
 
echo $twig->render('privacy-settings.html', $arrTwigVar);

?>