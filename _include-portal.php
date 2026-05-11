<?php

$domainName = $_SESSION[$class->loginSession]['customerCompany']['domain'];

if(empty($domainName)){
	header('location: /customer-portal'); 
	die;
}


//errror kalo pake session
$CUSTOMER_CONN = newConnection($domainName);
$security->oDbCon = $CUSTOMER_CONN;

require_once '_global.php';

// cek akses 
$allowedURL = array_column($arrTwigVar['portalMenu'],'url');


// kecuali beberapa file, di by pass
array_push($allowedURL, '/ajax-gps');
if(!in_array( str_replace('.php','',$_SERVER['PHP_SELF']),$allowedURL )) {
	header('location: /'); 
	die;
}


if(!$security->isMemberLogin(false))  {
	header('location: /customer-portal'); 
	die;
}


// SET ULANG POST KALO AD KIRIMAN DARI GET
foreach($_GET as $key=>$getRow){ 
	if(isset($_GET[$key]) && !empty($_GET[$key]))
		$_POST[$key] = $_GET[$key]; 
}


//initStorage
// pake bawaan class sepertinya mengarah ke wintera terus,jadinya gagal S3 nya
if(!defined('STORAGE')){
     define('STORAGE',array(
                'name' => 'S3',
                'domain' => S3['domain'],
                'bucket' => S3['bucket'],
                'endpoint' => S3['endpoint'],
                'accesskey' => S3['accesskey'],
                'secretkey' => S3['secretkey'], 
            ));                      

}



$arrTwigVar ['title'] =  'Transportation Management System'; 
$arrTwigVar ['customerCompany'] = $_SESSION[$class->loginSession]['customerCompany'];

$arrTwigVar['inputHidStatusCriteria'] =  $class->inputHidden('hidStatusCriteria');
$arrTwigVar['inputSearch'] =  $class->inputText('txtSearch',array('etc' => ' style="width:18em; float:right" placeholder="'.$class->lang['search'].'"'));
$arrTwigVar['inputButton'] =  $class->inputSubmit('btnSubmit', $class->lang['search']);
$arrTwigVar['inputFilterButton'] =  $class->inputSubmit('btnFilterSubmit', $class->lang['updateFilter'], array('add-class' => 'btn-filter'));

if(!isset($_POST['trStartDatePeriod'])) $_POST['trStartDatePeriod'] = date('01 / 01 / Y');
if(!isset($_POST['trEndDatePeriod'])) $_POST['trEndDatePeriod'] = date('d / m / Y');

$arrTwigVar['inputChkDatePeriod'] =  $class->inputCheckBox('chkDatePeriodFilter');
$arrTwigVar['useDatePeriodFilter'] = (isset($_POST['chkDatePeriodFilter']) && $_POST['chkDatePeriodFilter'] == 1) ? 1 : 0;
$arrTwigVar['inputDatePeriodFrom'] =  $class->inputDate('trStartDatePeriod', array('add-class' => 'input-date', 'etc' => 'style="text-align:center;"'));
$arrTwigVar['inputDatePeriodTo'] =  $class->inputDate('trEndDatePeriod', array('add-class' => 'input-date', 'etc' => 'style="text-align:center;"'));

function updateGetParameters(){
	 
	// set GET parameter utk paging
	$arrReturn = array();
		
	if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch']))
		array_push( $arrReturn, array('key' => 'txtSearch','value' => $_POST['txtSearch']));
 

	if(isset($_POST['hidStatusCriteria']) && !empty($_POST['hidStatusCriteria']))
		array_push( $arrReturn, array('key' => 'hidStatusCriteria','value' => $_POST['hidStatusCriteria']));
 
	if(isset($_POST['chkDatePeriodFilter']) && !empty($_POST['chkDatePeriodFilter'])){
		array_push( $arrReturn, array('key' => 'chkDatePeriodFilter','value' => $_POST['chkDatePeriodFilter']));
		array_push( $arrReturn, array('key' => 'trStartDatePeriod','value' => $_POST['trStartDatePeriod']));
		array_push( $arrReturn, array('key' => 'trEndDatePeriod','value' => $_POST['trEndDatePeriod']));
	}
		
	return $arrReturn;
}
?>