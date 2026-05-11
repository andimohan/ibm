<?php 

includeClass(array('AP.class.php','APCommission.class.php','APPayment.class.php','APCommissionPayment.class.php','EMKLJobOrder.class.php','EMKLCommission.class.php'));
$apCommissionPayment = createObjAndAddToCol( new APCommissionPayment()); 
$emklJobOrder= createObjAndAddToCol( new EMKLJobOrder()); 
$emklCommission = createObjAndAddToCol( new EMKLCommission()); 
    

$obj = $apCommissionPayment;
$generateReportContent = function ($dataset){ 

$obj = new APCommissionPayment();    
$apCommission = $obj->getAPObj();

$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
$decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;    

$rate = ($rs[0]['currencykey'] == CURRENCY['idr']) ? 1 : $rs[0]['rate'] ;

$arrSupplierBankInf = array();
if(!empty($rs[0]['supplierbankname'])) array_push($arrSupplierBankInf,$rs[0]['supplierbankname']);
if(!empty($rs[0]['supplieraccountname'])) array_push($arrSupplierBankInf,$rs[0]['supplieraccountname']);
if(!empty($rs[0]['supplieraccountno']))array_push($arrSupplierBankInf,$rs[0]['supplieraccountno']);
	
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['apCommissionPayment']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">'.$obj->lang['supplier'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['suppliername'] .'</td></tr>    
<tr><td class="header-row-header">'.$obj->lang['paymentTo'].'</td><td style="text-align:center">:</td><td>'. implode(', ',$arrSupplierBankInf) .'</td></tr>    
<tr><td class="header-row-header">'.$obj->lang['taxIdentificationNumber'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['suppliertaxid'] .'</td></tr>    
<tr><td class="header-row-header">Rate</td><td style="text-align:center">:</td><td>'. $obj->formatNumber($rate,-2) .'</td></tr>    

</table> 
  
<div style="clear:both"></div> ';

$html .= '<table  cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '110'));
array_push($cellArray, array('label' => $obj->lang['refCode'], 'width' => '120' ));
array_push($cellArray, array('label' => $obj->lang['etd'], 'align' => 'center', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['shipper']));
array_push($cellArray, array('label' => $obj->lang['total'] . ' ('.$rs[0]['currencyname'].')', 'align' => 'right', 'width' => '100'));
if($rs[0]['currencykey'] != CURRENCY['idr'])
    array_push($cellArray, array('label' => $obj->lang['total'] . ' (IDR)', 'align' => 'right', 'width' => '100'));
    
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
 
$rsApCol = $apCommission->searchData('','',true, ' and '. $apCommission->tableName.'.pkey in ('. $obj->oDbCon->paramString( array_column($rsDetail, 'apkey'),',').')');
$rsApCol = array_column($rsApCol,null,'pkey');
	
for ($i=0;$i<count($rsDetail);$i++){   
  $rsAp = $rsApCol[$rsDetail[$i]['apkey']];

  $totalIdr = $rsDetail[$i]['amount'] * $rate;    
        
  $refCode = array();
  if(!empty($rsAp['refcode'])) array_push($refCode, $rsAp['refcode']) ;
  if(!empty($rsAp['refcode2'])) array_push($refCode, $rsAp['refcode2']) ;

  $html .= '<tr><td>'.$rsAp['code'].'</td><td>'.implode(', ',$refCode).'</td><td style="text-align:center">'. $obj->formatDBDate($rsAp['etdpol']) .'</td><td>'. $rsAp['customername'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount'],$decimalPrice).'</td>';
	 
  if($rs[0]['currencykey'] != CURRENCY['idr'])
    $html .='<td style="text-align:right">'.$obj->formatNumber($totalIdr,$decimalPrice).'</td>' ; 
     
  $html .= '</tr>';
}
$html .= '</table>' ;
     
$arrSubtotal = array(); 
 
if ($rs[0]['totaldiscount'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalDiscount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['totaldiscount'] * $rate * -1,$decimalPrice).'</td></tr>');
}
    

if ($rs[0]['payabletax23'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['payabletax23'] * $rate * -1,$decimalPrice).'</td></tr>');
}
    
if ($rs[0]['totaldownpayment'] > 0){ 
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment'] * $rate * -1,$decimalPrice).'</td></tr>'); 
}
    
$rsARCost = $obj->getCostDetail($rs[0]['pkey']);  
for ($j=0;$j<count($rsARCost);$j++){  
 array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($rsARCost[$j]['costname']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rsARCost[$j]['amount']* $rate,$decimalPrice).'</td></tr>'); 
}
    
if (!empty($arrSubtotal)){ 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']* $rate,$decimalPrice).'</td></tr>'); 
} 
    
if ($rs[0]['totalpayment'] != 0) { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']* $rate,$decimalPrice).'</td></tr>'); 
    
    $balance = $rs[0]['totalpayment']-$rs[0]['grandtotal'];
    if($balance <> 0)
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($balance* $rate,$decimalPrice).'</td></tr>');   
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal'] * $rate);
    
$payment = '';
$rsARPaymentMethodDetail = (ADV_FINANCE && TEST_VOUCHER) ? $obj->getPaymentVoucherDetail($rs[0]['pkey'],'',2) : $obj->getPaymentMethodDetail($rs[0]['pkey']);  
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';
  
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
        
    $paymentMethodeName =  (ADV_FINANCE && TEST_VOUCHER) ? $rsARPaymentMethodDetail[$j]['vouchercode'] : $rsARPaymentMethodDetail[$j]['paymentmethodname'];

    $payment .= '<tr>';
    $payment .= '<td style="width: 120px">'.$paymentMethodeName.'</td>';
    $payment .= '<td style="text-align:center; width: 15px;">:</td>';
    $payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
    $payment .= '</tr>'; 
 
}

$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>'.$obj->lang['saidAmount'].' </strong> :<br>'.ucwords($sayNumber).'<br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalpaid']* $rate,$decimalPrice).'</td></tr>
';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';  

$html .= '
<table cellpadding="4">
<tr><td><strong>'.$obj->lang['note'].'</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
