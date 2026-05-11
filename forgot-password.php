<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar['inputEmailPlaceholder'] = $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"'));

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['resetPassword']); 
$_POST['action'] ='recover-account';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['PAGE_NAME'] =  $class->lang['forgotPassword'];
 
echo $twig->render('forgot-password.html', $arrTwigVar);

?>
