<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
 
if($security->isMemberLogin(false)) 
	header('location:/'); 

$arrTwigVar ['inputUserName'] =  $class->inputText('loginID'); 
$arrTwigVar ['inputPassword'] =  $class->inputPassword('loginPassword'); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['login']); 
$_POST['action'] ='login';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
echo $twig->render('login.html', $arrTwigVar);

?>
