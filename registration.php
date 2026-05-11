<?php
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

// kalo member udah logged in, gk boleh masuk page ini
if($security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}


// dibawah ref, agar kecatat dulu sessionnya
// if(empty($_GET['type'])){
// 	header('location:/membership-plan.html'); 
// 	die;
// }

includeClass(array('Currency.class.php','Country.class.php'));
$country = new Country();
$currency = new Currency();

if(isset($_SESSION['referralCode']) && !empty($_SESSION['referralCode'])) $_POST['referralCode'] = $_SESSION['referralCode'];
	 
// $localInformation = $class->getIPLocalInformation($_SERVER['REMOTE_ADDR']);

$rsLangSession = array_column($rsLang,'currencykey','code');
$langkey = $rsLangSession[$_SESSION['lang']];

// $_POST['selCountry'] = $localInformation['countrykey'];
// $_POST['selMobileCode'] = $localInformation['phonecode'];
// $_POST['selNationality'] = $localInformation['countrykey'];
$_POST['hidCurrencyKey'] = $langkey;
$_POST['selLang'] = $langkey;

$rsCurrency = $currency->searchDataRow(array($currency->tableName.'.pkey',$currency->tableName.'.code'),' and '.$currency->tableName.'.pkey = ' . $currency->oDbCon->paramString($langkey));

if($class->isActiveModule('membershipLevel')){ 

	includeClass(array('MembershipLevel.class.php'));
	$membershipLevel = new MembershipLevel();
 
	$rsLevel = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',  $membershipLevel->tableName.'.name', 'lower('.$membershipLevel->tableName.'.name) as lowername', $membershipLevel->tableName.'.sellingprice'),
													 ' and '.$membershipLevel->tableName.'.statuskey = 1');
	$arrMembership = $class->generateComboboxOpt(array('data' =>$rsLevel )); 
 
	$arrType = array_column($rsLevel,null,'lowername');

	// ambil default membership level kalo blm ada / tdk dikirim
	$selectedType = (!empty($_GET['type']) && isset($arrType[$_GET['type']])) ? $arrType[$_GET['type']] : $membershipLevel->getDefaultLevel()[0];
	
	$_POST['selMembership']  = $selectedType['pkey'];
	
	$arrTwigVar ['inputSelMembership']  = $class->inputSelect('selMembership',$arrMembership);  
	$arrTwigVar ['inputHidSelMembership']  = $class->inputHidden('selMembership');  
	$arrTwigVar ['packageName'] = $selectedType['name'];

    $rsPrice = $membershipLevel->getMembershipPrice($selectedType['pkey'],$langkey);
	$arrTwigVar ['packagePrice'] = $rsPrice[0]['sellingprice'];
}

$arrTwigVar ['currencyCode'] = $rsCurrency[0]['code'];
$arrTwigVar ['inputPassword'] =  $class->inputPassword('password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar ['inputUserName'] =  $class->inputText('userName'); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('mobile'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 

$arrTwigVar['inputUserNamePlaceholder'] = $class->inputText('userName', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPasswordPlaceholder'] = $class->inputPassword('password', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 
$arrTwigVar['inputPasswordConfirmationPlaceholder'] = $class->inputPassword('passwordConfirmation', array( 'etc' => 'placeholder="'.$class->lang['passwordConfirmation'].'"')); 
$arrTwigVar['inputNamePlaceholder'] = $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar['inputPhonePlaceholder'] = $class->inputText('mobile', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar['inputEmailPlaceholder'] = $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"'));

// $arrMobileCode = $class->generateComboboxOpt(array('data' => $country->getPhoneCode(),'label' => 'plusphonecode', 'value' => 'phonecode'));
// $arrTwigVar ['inputSelMobileCode']  = $class->inputSelect('selMobileCode', $arrMobileCode, array('etc' => 'style="padding-right:0"'));  
$arrTwigVar ['inputSelLang'] =  $class->inputSelect('selLang', $class->generateComboboxOpt(array('data' => $rsLang))); 

// $arrNationality = $class->generateComboboxOpt(array('data' => $country->getNationality(), 'label' => 'nationality', 'value' => 'pkey'));
// $arrTwigVar ['inputSelNationality']  = $class->inputSelect('selNationality', $arrNationality, array('etc' => 'style="padding-right:0"'));  

// $rsCountry = $country->searchDataRow(array($country->tableName.'.pkey',  $country->tableName.'.name'), ' and '.$country->tableName.'.statuskey = 1');
// $arrTwigVar ['inputSelCountry'] =  $class->inputSelect('selCountry', $class->generateComboboxOpt(array('data' => $rsCountry))); 
// $_POST['selTimeZone'] = $localInformation['timezone']['gmt'];
// $arrTwigVar ['inputHidGMT'] =  $class->inputHidden('selTimeZone'); 
// $arrTwigVar ['inputHidCurrency'] =  $class->inputHidden('hidCurrencyKey'); 

$arrTwigVar ['inputChkAgreement'] = $class->inputCheckBox('chkAgree');
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['register']); 


$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputHidReferral'] =  $class->inputHidden('referralCode'); 
 
echo $twig->render('registration.html', $arrTwigVar);
?>