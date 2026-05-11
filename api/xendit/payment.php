<?php
require_once '../../_config.php';  
require_once '../../_include-fe-v2.php';
require_once '../../_global.php';  // perlu utk obj $twig utk kirim email

includeClass( array('Xendit.class.php') );

$xendit = new Xendit();
$callbackToken  = $class->loadSetting('PaymentGatewayCallbackToken');
$header = $security->getHeaders();

if($header['X-Callback-Token'] != $callbackToken) { 
    $class->setLog('callback token not match',true,'xendit');
    die;
}

$data = json_decode(file_get_contents('php://input'), true);

$result = $xendit->invoicePaid($data);

?>