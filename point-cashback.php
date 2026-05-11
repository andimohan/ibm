<?php 
require_once '_config.php'; 
include '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('Customer.class.php'));
$customer = new Customer();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

$_POST['action'] ='pointcashback';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point',$customer->tableName.'.membershiplevel',$customer->tableName.'.canusepoint'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));
$rewardPointValue = $class->loadSetting('rewardsPointUnitValue');

$minimumPoint = $class->loadSetting('minimumFirstPoint'); 

$point = $rsCustomer[0]['point'];
$amount = $point * $rewardPointValue;
 
$arrTwigVar ['eligiblePoint'] = $point ;  
$arrTwigVar ['cashbackAmount'] = $amount ;  
$arrTwigVar ['minimumPoint'] =  $minimumPoint;  
$arrTwigVar ['membershipLevel'] =   $rsCustomer[0]['membershiplevel'];  
$arrTwigVar ['canUsePoint'] =   $rsCustomer[0]['canusepoint']; 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$lang->lang['claimCashback']); // untuk checkout manual
$arrTwigVar ['ACTIVE_MENU'] =  array('/point-cashback.php');

echo $twig->render('point-cashback.html', $arrTwigVar);

?>