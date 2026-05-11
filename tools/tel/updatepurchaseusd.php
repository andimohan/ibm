<?php

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php';
$emklPurchaseOrder = new EMKLPurchaseOrder(); 
 
$defaultTOPKey = $termOfPayment->getDefaultData();
$usdkey = 2;
$sql = 'select * from emkl_purchase_order_header where statuskey in(1,2,3) and rate > 1 and currencykey = '. $class->oDbCon->paramString($usdkey);
$rs = $class->oDbCon->doQuery($sql);

echo count($rs).' rows <br>';

foreach ($rs as $purchaseRow){
    
    $emklPurchaseOrder->setLog($purchaseRow['pkey'] . ' => ' .$purchaseRow['code'],true);
    
    $class->oDbCon->startTrans();
    
	$rsDetail = $emklPurchaseOrder->getDetailWithRelatedInformation($purchaseRow['pkey']);
	if(empty($rsDetail)) continue;
    
	echo $purchaseRow['code'].'<br>';
    
	$rate = $purchaseRow['rate'];
	$countDetail = count($rsDetail);
	 
	$subTotal = 0;
	for($i=0;$i<$countDetail;$i++){
		$qty = $rsDetail[$i]['qty'];
		$priceInUnit = $rsDetail[$i]['priceinunit'];
		$detailCurrencySubtotal = $qty * $priceInUnit;
		$detailSubtotal = $detailCurrencySubtotal;
        
		if($rsDetail[$i]['currencykey'] == CURRENCY['idr'])
			$detailSubtotal /= $rate;
		
		$subTotal += $detailSubtotal;
		$sql = 'update  '.$emklPurchaseOrder->tableNameDetail.' 
                set subtotal = '.$detailSubtotal.',
                subtotalcurrency = '.$detailCurrencySubtotal.' 
                where '.$emklPurchaseOrder->tableNameDetail.'.pkey =  '.$rsDetail[$i]['pkey'];
		$class->oDbCon->execute($sql);
	}
    
	$beforeTaxTotal = $subTotal;
	$grandtotal = $beforeTaxTotal;
	$taxPercentage = $purchaseRow['taxpercentage'];
	$taxValue = 0;
	
	if ($purchaseRow['ispriceincludetax'] == false) {
		$taxValue = $beforeTaxTotal * $taxPercentage / 100;
		$grandtotal += $taxValue;
	}else{
		$taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
		$beforeTaxTotal = $grandtotal - $taxValue ;
	}
	
	$totalPayment = 0;
	if($purchaseRow['termofpaymentkey']==$defaultTOPKey){
		$rsPaymentMethodDetail = $emklPurchaseOrder->getPaymentMethodDetail($purchaseRow['pkey']);
		$countPayment = count($rsPaymentMethodDetail);
		for($i=0;$i<$countPayment;$i++){
			$paymentAmount = $rsPaymentMethodDetail[$i]['amount'] / $rate;
			$totalPayment += $paymentAmount;
			$sql = 'update  '.$emklPurchaseOrder->tablePayment.' set amount = '.$paymentAmount.' where '.$emklPurchaseOrder->tablePayment.'.pkey =  '.$rsPaymentMethodDetail[$i]['pkey'];
			$class->oDbCon->execute($sql);
		} 
	}
	
	$balance = 0;
     
	$balance = $totalPayment - $grandtotal;
	$sql = 'update  '.$emklPurchaseOrder->tableName.' set subtotal = '.$subTotal.',beforetaxtotal = '.$beforeTaxTotal.',taxvalue = '.$taxValue.',balance = '.$balance.',grandtotal = '.$grandtotal.',totalpayment = '.$totalPayment.' where '.$emklPurchaseOrder->tableName.'.pkey =  '.$purchaseRow['pkey'];
	$class->oDbCon->execute($sql);
    
    $class->oDbCon->endTrans();
    
//	echo $sql ;
}
echo '<bR><br>done ';
?>