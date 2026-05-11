<?php 
require_once '_config.php'; 
include '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('SalesOrder.class.php','Item.class.php'));
$salesOrder = new SalesOrder();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$arrTwigVar ['pageIndex'] =  $pageIndex;

$totalrowsperpage = $class->loadSetting('historyTransactionTotalPerPage');
if (empty($totalrowsperpage))
    $totalrowsperpage = 10;
 

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
      
$criteria = ' and customerkey = ' . $salesOrder->oDbCon->paramString(USERKEY);

$rsSalesOrder = $salesOrder->searchData('','',true,$criteria,'order by '.$salesOrder->tableName.'.pkey desc',$limit);
for($i=0;$i<count($rsSalesOrder);$i++){
	$rsSalesOrder[$i]['invoicetoken'] = md5($rsSalesOrder[$i]['pkey'] . $rsSalesOrder[$i]['grandtotal'] . $salesOrder->secretKey);
}

$rsSalesOrderDetail = $salesOrder->getDetailWithRelatedInformation(array_column($rsSalesOrder,'pkey'));
$rsSalesOrderDetail = $salesOrder->reindexDetailCollections($rsSalesOrderDetail,'refkey');

$rsVoucher = $salesOrder->getVoucherDetail(array_column($rsSalesOrder,'pkey'));

// Gabungkan rsVoucher ke dalam setiap salesOrder berdasarkan refkey
$rsVoucherByRefkey = array();
foreach($rsVoucher as $voucher) {
	if(!isset($rsVoucherByRefkey[$voucher['refkey']]))
		$rsVoucherByRefkey[$voucher['refkey']] = array();
	$rsVoucherByRefkey[$voucher['refkey']][] = $voucher;
}

for ($i = 0; $i < count($rsSalesOrder); $i++) {
	$rsSalesOrder[$i]['vouchers'] = isset($rsVoucherByRefkey[$rsSalesOrder[$i]['pkey']]) ? $rsVoucherByRefkey[$rsSalesOrder[$i]['pkey']] : array();
}
for ($i=0;$i<count($rsSalesOrder);$i++) {
    $rsSalesOrder[$i]['token'] = md5($rsSalesOrder[$i]['pkey'] .$rsSalesOrder[$i]['grandtotal'] . $salesOrder->secretKey);

    $rsSalesOrder[$i]['paymentInformation'] = '';
    
    // set informasi pembayaran
	// hanya muncul jika blm ad pembayaran bkn dr statuskey, bisa saja sudah byr tp blm diproses 
	if($rsSalesOrder[$i]['paymentgatewaysuccess'] == 0 && !empty($rsSalesOrder[$i]['paymentgatewayinvoiceurl']))
		$rsSalesOrder[$i]['paymentInformation'] = '<a href="'.$rsSalesOrder[$i]['paymentgatewayinvoiceurl'].'" target="_blank">'.$class->lang['paymentInstruction'].'</a>';

	$rsOrderDetail = $rsSalesOrderDetail[$rsSalesOrder[$i]['pkey']];
	foreach($rsOrderDetail as $detailkey=>$itemDetail){  
		 
        // kalo variant, gk ad image, ambil image parentnya 
        $rsItemImage = $item->getItemImage($itemDetail['itemkey']);
        if($itemDetail['isvariant'] == 1 && empty($rsItemImage))
            $rsItemImage = $item->getItemImage($itemDetail['itemparentkey']);
		 
		//if($rsSalesOrder[$i]['code'] == 'SO00015')
		//	$salesOrder->setLog($rsItemImage,true);
		
		if(!empty($rsItemImage))
			$rsOrderDetail[$detailkey]['image'] = $rsItemImage;
		 
	}
	
    $rsSalesOrder[$i]['detail'] = $rsOrderDetail; 
	
}



$totalPages = ceil( $salesOrder->getTotalRows($criteria) / $totalrowsperpage); 

$arrTwigVar ['rsSalesOrder'] =  $rsSalesOrder;
$arrTwigVar ['totalPages'] =  $totalPages;   
  
echo $twig->render('transaction-history.html', $arrTwigVar);

?>
