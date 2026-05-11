<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 

includeClass(array('Customer.class.php'));
$customer = new Customer();

$_POST['action'] ='add-multi-address';  

$arrTwigVar ['inputPassword'] =  $class->inputPassword('loginPassword'); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array('etc' => 'style="height:8em"')); 
$arrTwigVar ['inputZipCode'] =  $class->inputText('zipCode'); 

$arrTwigVar ['btnSubmit'] =   $class->inputButton('btnSave',$class->lang['save']); 
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$arrTwigVar['loginRedirectURL'] = $redirectUrl;
$arrTwigVar ['PAGE_NAME'] =  $class->lang['address'];

echo $twig->render('popup-multi-address.html', $arrTwigVar);

?>