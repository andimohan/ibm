<?php 
require_once '_config.php'; 
require_once '_include-min.php'; 
require_once '_global.php';  
  
$arrTwigVar ['inputUserName'] =  $class->inputText('userId'); 
$arrTwigVar ['inputPassword'] =  $class->inputPassword('userPassword'); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnCheckin',$class->lang['checkIn']); 
$_POST['action'] ='checkin';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
echo $twig->render('checkin.html', $arrTwigVar); 
?>
