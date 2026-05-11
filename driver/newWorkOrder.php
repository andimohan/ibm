<?php   
require_once '../_config.php'; 
require_once '../_include.php'; 
include '_global.php';

$obj = $truckingServiceWorkOrder; 

$arrTwigVar['inputHidWOCode'] = $obj->inputText('hidWOCode'); 
$arrTwigVar['inputWOCode'] = $obj->inputText('woCode',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputVerificationCode'] = $obj->inputText('verificationCode',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['btnSave'] = $obj->inputButton('btnSave',$obj->lang['submit']);

echo $twig->render('newWorkOrder.html', $arrTwigVar); 
?>
