<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';

$domainName = (isset($_SESSION[$class->loginSession]['customerCompany']['domain'])) ? $_SESSION[$class->loginSession]['customerCompany']['domain'] : '';
//$class->setLog($class->loginSession,true);
//$class->setLog($_SESSION,true);


if(!empty($domainName)){
	$GLOBALS['oDbCon'] = newConnection($domainName);
	$security->oDbCon = $GLOBALS['oDbCon'];
}
 
// sudah ke kick duluan disini, di page lain jg ke kick, karena connection domainnya beda
require_once '_global.php';  

if($security->isMemberLogin(false))  {
	header('location: /dashboard'); 
	die;
}

//if(!$security->isAdminLogin('SecurityPrivileges',10,true)); 
//if(!empty(USERKEY)){
//	header('location: /dashboard'); 
//	die;
//}

 
$arrTwigVar ['inputCodeCompany'] =  $class->inputText('companyCode'); 
$arrTwigVar ['inputUserName'] =  $class->inputText('loginID'); 
$arrTwigVar ['inputPassword'] =  $class->inputPassword('loginPassword'); 

$arrTwigVar['inputUserNamePlaceholder'] = $class->inputText('loginID', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPasswordPlaceholder'] = $class->inputPassword('loginPassword', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 


$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['login']); 
$_POST['action'] ='login';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action');

echo $twig->render('customer-portal.html', $arrTwigVar);

?>
