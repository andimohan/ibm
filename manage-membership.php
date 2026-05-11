<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  
 
if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('MembershipLevel.class.php','MembershipSubscription.class.php')); 
$membershipLevel = new MembershipLevel();
$membershipSubscription = new MembershipSubscription();

$rsSubscription = $membershipSubscription->getAvailableSubscription($LOGIN_USER['pkey']);

$arrMembership = $class->generateComboboxOpt(array('data' =>$rsSubscription, 'label' => 'label' )); 
  
$_POST['selMembership'] = $LOGIN_USER['membershiplevel'];
$arrTwigVar ['inputSelMembership']  = $class->inputSelect('selMembership',$arrMembership);  
$arrTwigVar ['hasSubscriptionOpt'] = (empty($arrMembership)) ? false : true; 
$arrTwigVar ['btnUpgrade'] =   $class->inputSubmit('btnSave',$lang->lang['submit']);

$_POST['action'] ='add';  
$arrTwigVar['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar['rsSubscription'] = array_column($rsSubscription,null,'pkey');
  
array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
echo $twig->render('manage-membership.html', $arrTwigVar);

?>