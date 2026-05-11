<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

// ditembak di ajax
//$rs = $customer->getDataRowById(USERKEY); 
//$arrTwigVar ['inputHidId'] =  $class->input('hidden','hidId'); 
//$id = $rs[0]['pkey'];

$_POST['action'] ='update-password';  
$arrTwigVar ['inputHidAction'] = $class->inputHidden('action');  
$arrTwigVar ['inputCurrentPassword'] =   $class->inputPassword('currentPassword');
$arrTwigVar ['inputNewPassword'] = $class->inputPassword('password');  
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar['btnSubmit'] =  $class->inputSubmit('btnSave',$class->lang['save']); 

echo $twig->render('update-password.html', $arrTwigVar);

?>