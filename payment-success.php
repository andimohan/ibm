<?php 
include '_config.php';  
include '_include-fe-v2.php'; 
include '_global.php'; 
     
includeClass("SalesOrder.class.php","Shipment.class.php");
$salesOrder = new SalesOrder();

$arrTwigVar ['title'] =  $class->lang['paymentInformation'];
//$arrTwigVar ['content'] =  $class->lang['paymentSuccessContent'];

//echo $twig->render('page.html', $arrTwigVar);  
if(!isset($_GET) || empty($_GET['id'])) die;
if(!isset($_GET) || empty($_GET['token'])) die;

$pkey = $_GET['id'];
$token = $_GET['token'];
$rs = $salesOrder->getDataRowById($pkey);

$transactionToken = $salesOrder->getTransactionToken($rs);
if ($transactionToken <> $token)  die;

$rs[0]['token'] = $transactionToken;
$arrTwigVar['rs'] =  $rs[0];

echo $twig->render('payment-success.html', $arrTwigVar);  

?>
