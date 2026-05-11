<?php 
require_once '_config.php'; 
include '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('SalesOrder.class.php','Item.class.php','Biteship.class.php'));
$salesOrder = new SalesOrder();
$item = new Item();
$biteShip = new Biteship();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 

if(!isset($_GET) || empty($_GET['id'])){
	header("location: /");
	die;
}

$pkey = $_GET['id'];
$criteria = ' and '.$salesOrder->tableName.'.pkey = '.$salesOrder->oDbCon->paramString($pkey).' 
			 and '.$salesOrder->tableName.'.customerkey = '.$salesOrder->oDbCon->paramString(USERKEY);

$rsSalesOrder = $salesOrder->searchData('','',true,$criteria);
$rsSalesOrder[0]['invoicetoken'] = md5($rsSalesOrder[0]['pkey'] . $rsSalesOrder[0]['grandtotal'] . $salesOrder->secretKey);
$rsVoucher = $salesOrder->getVoucherDetail($rsSalesOrder[0]['pkey']);
$totalVoucherAmount = 0;
foreach($rsVoucher as $voucherDetail){
	$totalVoucherAmount += $voucherDetail['amount'];
}
$rsSalesOrder[0]['totalvoucher'] = $totalVoucherAmount;							   
if(empty($rsSalesOrder)) die;

$rsSalesOrderDetail = $salesOrder->getDetailWithRelatedInformation(array_column($rsSalesOrder,'pkey'));

$rsItemReview =  $item->getReview(' and '.$item->tableReview.'.salesorderkey = '.$item->oDbCon->paramString($pkey));
$rsItemReview = array_column($rsItemReview, null,'refkey');

foreach($rsSalesOrderDetail as $detailkey=>$itemDetail){  

	// kalo variant, gk ad image, ambil image parentnya 
	$rsItemImage = $item->getItemImage($itemDetail['itemkey']);
	if($itemDetail['isvariant'] == 1 && empty($rsItemImage))
		$rsItemImage = $item->getItemImage($itemDetail['itemparentkey']);
  
	if(!empty($rsItemImage))
		$rsSalesOrderDetail[$detailkey]['image'] = $rsItemImage;


	$arrReview = $rsItemReview[$itemDetail['itemkey']];

	$_POST['hidItemKey'] = $itemDetail['itemkey'];
	$arrTwigVar['rsSalesOrderDetail'][$detailkey] = $rsSalesOrderDetail[$detailkey];
	$arrTwigVar['rsSalesOrderDetail'][$detailkey]['inputHidItemKey'] =  $class->inputHidden('hidItemKey');   
	$arrTwigVar['rsSalesOrderDetail'][$detailkey]['review'] =  $arrReview['review'];   
	$arrTwigVar['rsSalesOrderDetail'][$detailkey]['rating'] =  $arrReview['rating'];   

	$arrTwigVar['rsReview'][$detailkey]['review'] = $salesOrder->inputTextArea('review', array(
		'etc'   => 'style="height:6em;" disabled',
		'value' => $arrReview['review']
	));
}

if(!empty($rsSalesOrder[0]['trackingid'])){
	$trackingHistory = $biteShip->trackingOrder($rsSalesOrder[0]['trackingid']);
	$track = $trackingHistory['history'];
}

$rsSalesOrder[0]['trdate'] = date('d M Y H:i:s', strtotime($rsSalesOrder[0]['trdate']));
$paymentExpired = strtotime($rsSalesOrder[0]['trdate']) + (24 * 60 * 60);
$rsSalesOrder[0]['paymentexpired'] = date('d M Y H:i:s', $paymentExpired);
	
$_POST['action'] = 'addReview'; 
$_POST['hidId'] =$_GET['id'];  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId');
$arrTwigVar ['rsSalesOrder'] =  $rsSalesOrder;  
$arrTwigVar ['trackOrder'] =  $track;  
$arrTwigVar ['inputReview'] =  $salesOrder->inputTextArea('review', array('etc' => 'style="height:6em;"'));
$arrTwigVar ['inputHidRating'] =  $class->inputHidden('hidRating');
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSubmit',$class->lang['done']); 
echo $twig->render('transaction-history-detail.html', $arrTwigVar);

?>
