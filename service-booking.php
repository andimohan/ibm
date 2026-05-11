<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
  
$rsCompany = $company->searchData($company->tableName.'.statuskey',1,true,' and '. $company->tableName .'.isservice  = 1');
$rsCar = $car->searchData('', '', true, 'and customerkey = ', USERKEY);

$arrCar = $class->convertForCombobox($rsCar,'pkey','policenumber');
$arrCompany = $class->convertForCombobox($rsCompany,'pkey','name'); 
 
$_POST['trDate'] = date('d / m / Y');
$_POST['action'] ='edit';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputBookingDate'] =  $class->inputDate('trDate');
$arrTwigVar ['inputCompany'] =  $class->inputSelect('selCompany', $arrCompany);
$arrTwigVar ['inputCar'] =  $class->inputSelect('selCar', $arrCar);
$arrTwigVar ['inputDescription'] =  $class->inputTextArea('description', array('etc' => 'style="height:10em"'));
 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 
 
echo $twig->render('service-booking.html', $arrTwigVar);

?>
