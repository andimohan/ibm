<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}
 
require_once '_include-customer-information.php';

includeClass(array('Customer.class.php'));
$customer = new Customer(); 

$rs = $customer->getDataRowById(USERKEY);
$rsShippingAddress = $customer->getMultipleAddress(USERKEY,1,'',' order by pkey desc');

$_POST['hidModifiedOn'] =  $rs[0]['modifiedon']; 

$_POST['action'] ='edit-multi-address';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action');  
$_POST['action'] ='add-multi-address';  
$arrTwigVar ['inputHidActionNewAddress'] =  $class->inputHidden('action');  
 
$arrTwigVar ['token'] =  USERKEY;  
$arrTwigVar['hidModifiedOn'] = $class->inputHidden('hidModifiedOn');  
$arrTwigVar['PAGE_NAME'] = $class->lang['profile'] ;
$arrTwigVar['rsShippingAddress'] = $rsShippingAddress;


// untuk template row
// harus diatas agar tidak keisi post
$arrTwigVar['inputName'] = $class->inputText('maName[]');  
$arrTwigVar['inputPIC'] = $class->inputText('maPIC[]');  
$arrTwigVar['inputPhone'] = $class->inputText('maPhone[]');  
$arrTwigVar['inputAddress'] = $class->inputTextArea('maAddress[]', array('etc' => 'style="height:12em"')); 
$arrTwigVar['inputDesc'] = $class->inputTextArea('maTrDesc[]', array('etc' => 'style="height:12em"')); 
$arrTwigVar['inputZipCode'] = $class->inputText('maZipCode[]');  
$arrTwigVar['hidDetailKey'] = $class->inputHidden('hidDetailKey[]');  
$arrTwigVar['hidLatLngAdd'] = $class->inputHidden('hidLatLngAdd[]');
$arrTwigVar['inputChkPrimary'] = $class->inputCheckBox('maPrimary[]');  
$arrTwigVar['MAP_API_KEY'] = $class->loadSetting('mapAPIKey'); 

foreach($rsShippingAddress as $key=>$row){
	
	$_POST['maName[]'] = $row['name'];
	$_POST['maPIC[]'] = $row['pic'];
	$_POST['maPhone[]'] = $row['phone'];
	$_POST['maAddress[]'] = $row['address'];
	$_POST['maZipCode[]'] = $row['zipcode'];
	$_POST['maTrDesc[]'] = $row['trdesc'];
	$_POST['hidDetailKey[]'] = $row['pkey'];
	$_POST['hidLatLngEdit[]'] = $row['latlng'];
	$_POST['maPrimary[]'] = $row['isprimary'];

	$arrTwigVar['rsShippingAddress'][$key]['inputName'] = $class->inputText('maName[]');  
	$arrTwigVar['rsShippingAddress'][$key]['inputPIC'] = $class->inputText('maPIC[]');  
	$arrTwigVar['rsShippingAddress'][$key]['inputPhone'] = $class->inputText('maPhone[]');  
	$arrTwigVar['rsShippingAddress'][$key]['inputAddress'] =  $class->inputTextArea('maAddress[]', array('etc' => 'style="height:12em"'));
	$arrTwigVar['rsShippingAddress'][$key]['inputDesc'] =  $class->inputTextArea('maTrDesc[]', array('etc' => 'style="height:12em"'));
	$arrTwigVar['rsShippingAddress'][$key]['inputZipCode'] =  $class->inputText('maZipCode[]');  
	$arrTwigVar['rsShippingAddress'][$key]['hidDetailKey'] =  $class->inputHidden('hidDetailKey[]');   
	$arrTwigVar['rsShippingAddress'][$key]['hidLatLngEdit'] =  $class->inputHidden('hidLatLngEdit[]');  
	$arrTwigVar['rsShippingAddress'][$key]['inputChkPrimary'] =  $class->inputCheckBox('maPrimary[]');  
}

$arrTwigVar ['btnAddNewAdress'] =   $class->inputButton('btnAddNewAddress','+ ' .$class->lang['address']); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnEdit',$class->lang['save']); 
$arrTwigVar ['btnCancel'] =   $class->inputButton('btnCancel',$class->lang['cancel']);
 
array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
echo $twig->render('shipping-address.html', $arrTwigVar);

?>