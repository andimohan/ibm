<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("TruckingServiceWorkOrder.class.php"));
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$car = new Car();

$truckingServiceWorkOrder->oDbCon =  $CUSTOMER_CONN;
$car->oDbCon =  $CUSTOMER_CONN;

if (!isset($_GET) || empty($_GET['wokey'])) die;

$wokey=$_GET['wokey'];
$rsSPK  = $truckingServiceWorkOrder->getDataRowById($wokey);

if(empty($rsSPK)) die;



// cek SPK masih valid gk
// sementara dari status SPK, kedepannya dari status Driver

if ($rsSPK[0]['statuskey'] <> 2) die;

$rsCar = $car->getDataRowById($rsSPK[0]['carkey']);
$rsSPK[0]['vehiclenumber'] = $rsCar[0]['policenumber'];

$arrTwigVar['rsSPK'] = $rsSPK[0];

echo $twig->render('work-order-map.html', $arrTwigVar);

?>