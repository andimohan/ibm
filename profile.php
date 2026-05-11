<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

$isActiveModule = $class->isActiveModule(array('MembershipLevel.class.php','CustomerFeatures'));

require_once '_include-customer-information.php';

includeClass(array('Customer.class.php','BusinessCategory.class.php','JobPosition.class.php'));
$customer = new Customer();
$city = new City();
$businessCategory = new BusinessCategory();
$jobPosition = new JobPosition();
$customerFeatures = new CustomerFeatures();
$country = new Country();

$rs = $customer->getDataRowById(USERKEY);

$rsCustBusiness = $customer->getCustomerBusinessDetail($rs[0]['pkey']);
$arrBusinessDetailKey =  array_column($rsCustBusiness,'refbusinesskey'); 
$rsCustomerFeatures = ($isActiveModule['customerfeatures']) ? $customerFeatures->getFeaturesDetail($rs[0]['pkey']) : array();

$_POST['selEmailPrivacyKey'] = $rs[0]['emailprivacykey'];
$_POST['selMobilePrivacyKey'] = $rs[0]['mobileprivacykey'];

$rsMembership = array();
if($class->isActiveModule('MembershipLevel')){  
	includeClass(array('MembershipLevel.class.php'));
	$membershipLevel = new MembershipLevel();
	$rsMembership = $membershipLevel->getDataRowById($rs[0]['membershiplevel']);
	$rs[0]['membershiplevelname'] = $rsMembership[0]['name'];
}


$_POST['userCode'] = $rs[0]['code'];
$_POST['userName'] = $rs[0]['username'];
$_POST['membership'] = $rsMembership[0]['name'];
$_POST['name'] = $rs[0]['name'];  
$_POST['phone'] = $rs[0]['phone']; 
$_POST['mobile'] = $rs[0]['mobile']; 
$_POST['email'] = $rs[0]['email']; 
$_POST['address'] = $rs[0]['address'];
$_POST['zipCode'] = $rs[0]['zipcode']; 
$_POST['fax'] = $rs[0]['fax'];
$_POST['hidCityKey'] = $rs[0]['citykey']; 
$_POST['hidId'] = $rs[0]['pkey']; 
$_POST['hidLatLng'] = $rs[0]['latlng']; 
$_POST['expDate'] = $class->formatDBDate($rs[0]['expdate']); 
$_POST['selLang'] =  $rs[0]['langkey']; 
$_POST['selTimeZone'] =  $rs[0]['gmt']; 
    
$_POST['sex'] = $rs[0]['sexkey']; 
$_POST['IDNumber'] = $rs[0]['idnumber'];
$_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth'];
$_POST['dob'] = (!empty($rs[0]['dateofbirth'])) ? $class->formatDBDate($rs[0]['dateofbirth'],'d / m / Y') : '';

$_POST['FBAccount'] = $rs[0]['fbaccount']; 
$_POST['IGAccount'] = $rs[0]['igaccount']; 
$_POST['mapAddress'] = $rs[0]['mapaddress']; 
$_POST['companyName'] = $rs[0]['companyname'];
$_POST['selBusiness'] = $rs[0]['mainbusinesskey']; 
$_POST['offerDescription'] = $rs[0]['offerdescription'];
$_POST['prospectDescription'] = $rs[0]['prospectdescription'];
$_POST['selJobPosition'] = $rs[0]['jobpositionkey'];
$_POST['selCountry'] = $rs[0]['countrykey'];
$_POST['selNationality'] = $rs[0]['nationalitykey'];
$_POST['selMobileCode'] = $rs[0]['mobilecode'];
$_POST['selBusinessDetailKey[]'] = $arrBusinessDetailKey;

$arrBusinessDetail = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								   	' and '.$businessCategory->tableName.'.statuskey = 1');

$arrBusinessDetail = $customer->generateComboboxOpt(array('data' => $arrBusinessDetail));  
$arrTwigVar ['inputSelBusinessDetailKey']  =  $class->inputSelect('selBusinessDetailKey[]', $arrBusinessDetail, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrJobPosition = $jobPosition->generateComboboxOpt(null,array('criteria' =>' and ('.$jobPosition->tableName.'.statuskey = 1)', 'order' =>  'order by '.$jobPosition->tableName.'.name asc')); 

$arrRoot = array();
$arrRoot[0]['name'] = '-----';
$arrRoot[0]['pkey'] = 0;
$arrSex = array_merge($arrRoot,$class->getSex()); 
$arrSex = $class->generateComboboxOpt(array('data' => $arrSex));

$rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['citykey'],true);
if (!empty($rsCity[0]['name'])) 
    $_POST['cityName'] = $rsCity[0]['citycategoryname'];
  
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId');

$_POST['action'] ='edit';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$arrTwigVar ['rs'] =  $rs[0];
$arrTwigVar ['rsMembership'] =  $rsMembership[0];
$arrTwigVar ['features'] = $rsCustomerFeatures;
$arrTwigVar ['inputCurrentPassword'] =  $class->inputPassword('currentPassword'); 
$arrTwigVar ['inputNewPassword'] =  $class->inputPassword('password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar ['inputUserCode'] =  $class->inputText('userCode', array('readonly' => true )); 
$arrTwigVar ['inputUserName'] =  $class->inputText('userName', array('readonly' => true )); 
$arrTwigVar ['inputMembership'] =  $class->inputText('membership',  array('readonly' => true )); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputMapAddress'] =  $class->inputText('mapAddress', array('class' => 'form-control search-address', 'etc' => 'placeholder="'.$class->lang['searchLocation'].'"')); 
$arrTwigVar ['hidLatLng'] = $class->inputHidden('hidLatLng');    
$arrTwigVar ['inputMembershipExpDate'] =  $class->inputText('expDate', array('readonly' => true)); 
$arrTwigVar ['referralLink'] =  HTTP_HOST.'j='.$rs[0]['code']; 
$arrTwigVar ['inputSelJobPosition'] =  $class->inputSelect('selJobPosition', $arrJobPosition);
$arrCountry =  $country->generateComboboxOpt(null,array('criteria' =>' and ('.$country->tableName.'.statuskey = 1)')); 
$arrTwigVar ['inputSelCountry']  = $class->inputSelect('selCountry', $arrCountry);  

$arrNationality = $class->generateComboboxOpt(array('data' => $country->getNationality(), 'label' => 'nationality', 'value' => 'pkey'));
$arrTwigVar ['inputSelNationality']  = $class->inputSelect('selNationality', $arrNationality);  

$arrTwigVar ['inputCity']  =  $class->inputAutoComplete(array(  
                                                            'element' => array('value' => 'cityName',
                                                                               'key' => 'hidCityKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            )
    
                                                          )
                                                    );  

// model lama, tp coba cek lg di KD memang perlu seperti ini atau gmana
//$autoCompleteCity =  $class->inputAutoComplete(array(  
//                                                            'element' => array('value' => 'cityName',
//                                                                               'key' => 'hidCityKey'),
//                                                            'source' =>array(
//                                                                                'url' => 'ajax-city.php',
//                                                                                'data' => array(  'action' =>'searchData' )
//                                                                            ) , 
//                                                            'explodeScript' => true
//    
//                                                          )
//                                                    );  
//
//$arrTwigVar ['JSScript']  = str_replace(array('<script type="text/javascript">','</script>'),array('',''),$autoCompleteCity['script']); 
//$arrTwigVar ['inputCity']  = $autoCompleteCity['input']; 

$readonlyField = ($rs[0]['ssotypekey'] <> 0) ? true : false;

$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputMobile'] =  $class->inputText('mobile');
$arrTwigVar ['inputEmail'] =  $class->inputText('email', array('readonly' => $readonlyField)); 
$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em"'));  
$arrTwigVar ['inputZipcode'] =  $class->inputText('zipCode');
$arrTwigVar ['inputFax'] =  $class->inputText('fax');
$arrTwigVar ['inputCompanyName'] =  $class->inputText('companyName'); 
$arrTwigVar ['inputOfferDescription'] =  $class->inputTextArea('offerDescription', array( 'etc' => 'style="height:10em"'));  
$arrTwigVar ['inputProspectDescription'] =  $class->inputTextArea('prospectDescription', array( 'etc' => 'style="height:10em"'));
$arrTwigVar ['inputBirthDate'] =  $class->inputDate('dob', array( 'add-class'=>'label-style')); 

if($class->isActiveModule('BusinessCategory')){
	includeClass('BusinessCategory.class.php');
	$businessCategory = new BusinessCategory();
	$editBusinessInactiveCriteria = ' or '.$businessCategory->tableName.'.pkey = ' . $businessCategory->oDbCon->paramString($rs[0]['mainbusinesskey']);
	$arrBusiness = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								   	' and '.$businessCategory->tableName.'.statuskey = 1 ' .$editBusinessInactiveCriteria 
								  );
	$arrBusiness = $class->generateComboboxOpt(array('data' => $arrBusiness));
	$arrTwigVar ['inputSelBusiness'] =  $class->inputSelect('selBusiness',$arrBusiness); 
}


if($class->isActiveModule('MembershipLevel')){  
    includeClass(array('MembershipLevel.class.php'));
	$membershipLevel = new MembershipLevel();
    
    
    $_POST['selEmailPrivacyKey'] = $LOGIN_USER['emailprivacykey'];
    $_POST['selMobilePrivacyKey'] = $LOGIN_USER['mobileprivacykey'];

	$rsMembership = $membershipLevel->getDataRowById($LOGIN_USER['membershiplevel']);
	
	// nanti kalo user free boleh lihat baru diudpate where nya
 	$rsMembershipLv = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',$membershipLevel->tableName.'.name'),
                                             ' and '.$membershipLevel->tableName.'.statuskey = 1 
											   and '.$membershipLevel->tableName.'.pkey > 1'
											);
    
    $arrNone = array();
    $arrNone[999]['name'] = $class->lang['private'];
    $arrNone[999]['pkey'] = 999;
    $rsMembershipLv = array_merge($rsMembershipLv,$arrNone);
    
    $arrMembershipLv = $class->generateComboboxOpt(array('data' => $rsMembershipLv));
    $arrTwigVar ['inputSelEmailPrivacy'] =  $class->inputSelect('selEmailPrivacyKey',$arrMembershipLv); 
    $arrTwigVar ['inputSelMobilePrivacy'] =  $class->inputSelect('selMobilePrivacyKey',$arrMembershipLv); 
    $arrTwigVar ['rsMembershipLv'] =  $rsMembershipLv;
     
}

$arrTwigVar ['inputCurrentPasswordPlaceholder'] =  $class->inputPassword('currentPassword', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 
$arrTwigVar ['inputNewPasswordPlaceholder'] =  $class->inputPassword('password', array( 'etc' => 'placeholder="'.$class->lang['newPassword'].'"')); 
$arrTwigVar ['inputPasswordConfirmationPlaceholder'] =  $class->inputPassword('passwordConfirmation', array( 'etc' => 'placeholder="'.$class->lang['passwordConfirmation'].'"')); 
$arrTwigVar ['inputUserNamePlaceholder'] =  $class->inputText('userName', array('readonly' => true ), array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar ['inputNamePlaceholder'] =  $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar ['inputIDNumberPlaceholder'] =  $class->inputText('IDNumber', array( 'etc' => 'placeholder="'.$class->lang['IDNumber'].'"')); 
$arrTwigVar ['inputBirthDatePlaceholder'] =  $class->inputDate('dob', array( 'etc' => 'placeholder="'.$class->lang['dateOfBirth'].'"','add-class'=>'label-style')); 
$arrTwigVar ['inputGenderPlaceholder'] =  $class->inputSelect('sex',$arrSex); 
$arrTwigVar ['inputPhonePlaceholder'] =  $class->inputText('phone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar ['inputMobilePlaceholder'] =  $class->inputText('mobile', array( 'etc' => 'placeholder="'.$class->lang['mobilePhone'].'"')); 
$arrTwigVar ['inputEmailPlaceholder'] =  $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar ['inputAddressPlaceholder'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em" placeholder="'.$class->lang['address'].'"')); 
$arrTwigVar ['inputAddressRowPlaceholder'] =  $class->inputText('address', array( 'etc' => 'placeholder="'.$class->lang['address'].'"')); 
$arrTwigVar ['inputZipcodePlaceholder'] =  $class->inputText('zipCode', array( 'etc' => 'placeholder="'.$class->lang['zipCode'].'"')); 
$arrTwigVar ['inputFaxPlaceholder'] =  $class->inputText('fax', array( 'etc' => 'placeholder="'.$class->lang['fax'].'"')); 
$arrTwigVar ['inputSelLang'] =  $class->inputSelect('selLang', $class->generateComboboxOpt(array('data' => $rsLang))); 
$arrTwigVar ['inputSelGMT'] =  $class->inputSelect('selTimeZone', $class->getGMT()); 
 
$arrMobileCode = $class->generateComboboxOpt(array('data' => $country->getPhoneCode(),'label' => 'plusphonecode', 'value' => 'phonecode'));
$arrTwigVar ['inputSelMobileCode']  = $class->inputSelect('selMobileCode', $arrMobileCode, array('etc' => 'style="padding-right:0"'));  
$rsDefaultCountry = $country->searchDataRow(array($country->tableName.'.pkey'),' and '.$country->tableName.'.systemVariable = 1');
$arrTwigVar ['defaultLangKey'] = (!empty($rsDefaultCountry)) ? $rsDefaultCountry[0]['pkey'] : 0;

/*
$arrTwigVar ['inputFBPlaceholder'] =  $class->inputText('FBAccount', array( 'etc' => 'placeholder="'.$class->lang['fbAccount'].'"', 'add-class'=>'medsos-account')); 
$arrTwigVar ['inputIGPlaceholder'] =  $class->inputText('IGAccount', array( 'etc' => 'placeholder="'.$class->lang['igAccount'].'"', 'add-class'=>'medsos-account')); 
*/ 


$arrTwigVar ['btnAddRows'] =  $class->inputLinkButton('btnAddItemRows' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button','etc' => 'attr-template="detail-row-template"'));
$arrTwigVar ['btnDeleteRows'] =  $class->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0;"'));

$arrTwigVar ['ssoTypeKey'] =  $rs[0]['ssotypekey']; 

$rsItemImage = array();
if( !empty($rs[0]['photofile'])){

	$sourcePath = $customer->defaultDocUploadPath.$customer->uploadFolder.USERKEY;
	$destinationPath = $customer->uploadTempDoc.$customer->uploadFolder.USERKEY; 
	 
	$customer->deleteAll($destinationPath); 
 
	if(!is_dir($destinationPath))  mkdir ($destinationPath,  0755, true); 
	$customer->fullCopy($sourcePath,$destinationPath); 

	$rsItemImage[0]['file'] =  $rs[0]['photofile'];
}

$_POST['action'] ='update-settings';  
$arrTwigVar ['inputHidActionSettings'] =  $class->inputHidden('action'); 


$arrTwigVar ['uploadFolder'] =  $customer->uploadFolder; 
$arrTwigVar ['arrImage'] =  $rsItemImage; 
$arrTwigVar ['token'] =  USERKEY; 

$_POST['hidModifiedOn'] =  $rs[0]['modifiedon']; 
$arrTwigVar['hidModifiedOn'] = $class->inputHidden('hidModifiedOn'); 

$arrTwigVar['currDate'] = date('Y-m-d'); 
$arrTwigVar['PAGE_NAME'] = $class->lang['profile'] ;

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 
 
array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
echo $twig->render('profile.html', $arrTwigVar);

?>