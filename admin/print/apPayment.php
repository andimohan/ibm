<?php 

includeClass(array('APPayment.class.php','EMKLPurchaseOrder.class.php', 'EMKLCommission.class.php','EMKLJobOrder.class.php' ));

$obj = new APPayment();      
$apPayment = createObjAndAddToCol( new APPayment());

$obj = $apPayment;
$generateReportContent = function ($dataset){ 

$obj = new APPayment();    
$ap = $obj->getAPObj();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
 
$periodeHTMTL = '';
if($rs[0]['usedateperiod'])
   $periodeHTMTL = '<tr><td class="header-row-header">Periode</td><td style="text-align:center">:</td><td>'. $obj->formatDBDate($rs[0]['startdateperiod']) .' - '.$obj->formatDBDate($rs[0]['enddateperiod']).'</td></tr>';
    
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PEMBAYARAN HUTANG</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Pemasok</td><td style="text-align:center">:</td><td>'. $rs[0]['suppliername'] .'</td></tr>    
'.$periodeHTMTL.'
</table> 
  
<div style="clear:both"></div> ';

$html .= '<table  cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['refCode'] ));
array_push($cellArray, array('label' => $obj->lang['invoiceReference'], 'width' => '120'));
array_push($cellArray, array('label' => $obj->lang['refDate'], 'align' => 'center', 'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['payingSettlement'], 'align' => 'right', 'width' => '110'));
array_push($cellArray, array('label' => $obj->lang['tax23'], 'align' => 'right', 'width' => '90'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
 
for ($i=0;$i<count($rsDetail);$i++){   
  $rsAp = $ap->getDataRowById($rsDetail[$i]['apkey']);
/*  $rsKey = $ap->getTableKeyAndObj($rsDetail[$i]['reftabletype']);   
  $purchaseOrder = $rsKey['obj'];
  $rsPO = $purchaseOrder->getDataRowById($rsAp[0]['refheaderkey']);*/
    
  $refCode = array();
  if(!empty($rsAp[0]['refcode'])) array_push($refCode, $rsAp[0]['refcode']) ;
  if(!empty($rsAp[0]['refcode2'])) array_push($refCode, $rsAp[0]['refcode2']) ;
  $html .= '<tr><td>'.$rsAp[0]['code'].'</td><td>'.implode(', ',$refCode).'</td><td>'.$rsAp[0]['refinvoicecode'].'</td><td style="text-align:center">'. $obj->formatDBDate($rsAp[0]['refdate'],'',array('returnOnEmpty' => true, 'value' => '')) .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['taxamount']).'</td></tr>' ; 
}
$html .= '</table>' ;
     
$arrSubtotal = array(); 
 
if ($rs[0]['totaldiscount'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalDiscount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['totaldiscount']).'</td></tr>');
}
    

if ($rs[0]['payabletax23'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['payabletax23'] * -1).'</td></tr>');
}
    
if ($rs[0]['totaldownpayment'] > 0){ 
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment'] * -1).'</td></tr>'); 
}
    
$rsARCost = $obj->getCostDetail($rs[0]['pkey']);  
for ($j=0;$j<count($rsARCost);$j++){  
 array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($rsARCost[$j]['costname']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rsARCost[$j]['amount']).'</td></tr>'); 
}
    
if (!empty($arrSubtotal)){ 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
} 
    
if ($rs[0]['totalpayment'] != 0) { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']).'</td></tr>'); 
    
    $balance = $rs[0]['totalpayment']-$rs[0]['grandtotal'];
    if($balance <> 0)
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($balance).'</td></tr>');   
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['payingOffAmount']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
if(empty(trim($sayNumber))) $sayNumber = 'Nol';
    
$payment = '';
//bedain untuk metode pembayaran jika menggunakan voucher 
$rsARPaymentMethodDetail = (ADV_FINANCE && TEST_VOUCHER) ? $obj->getPaymentVoucherDetail($rs[0]['pkey'],'',2) : $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="2">';
  
    
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
    

    //bedain tampilan untuk yang menggunakan voucher 
    $paymentMethodeName =  (ADV_FINANCE && TEST_VOUCHER) ? $rsARPaymentMethodDetail[$j]['vouchercode'] : $rsARPaymentMethodDetail[$j]['paymentmethodname'];
 
    $payment .= '<tr>';
    $payment .= '<td style="width: 120px">'.$paymentMethodeName.'</td>';
    $payment .= '<td style="text-align:center; width: 15px;">:</td>';
    $payment .= '<td style="text-align:right; width: 100px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
    $payment .= '</tr>'; 
    
    
}


$payment  .= '</table>'; 	
    
$downpaymentDetail = '';

if($rs[0]['totaldownpayment'] > 0) {
$downpaymentDetail .= '<br><strong>'.$obj->lang['downpayment'].'</strong><br><table cellpadding="2">';

$rsDownpayment = $obj->getDownpaymentDetail($rs[0]['pkey'],'',false);

foreach($rsDownpayment as $dpRow) {
    $downpaymentDetail .= '<tr>';
    $downpaymentDetail .= '<td style="width: 120px">'. $dpRow['refcode'] .'</td>';
    $downpaymentDetail .= '<td style="text-align:center; width: 15px;">:</td>';
    $downpaymentDetail .= '<td style="text-align:right; width: 100px;">'.$obj->formatNumber($dpRow['amount']).'</td>';
    $downpaymentDetail .= '</tr>'; 
}

$downpaymentDetail .='</table>';

}

    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong><br>'.ucwords($sayNumber).' Rupiah. <br>'.$downpaymentDetail .'<br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalpaid']).'</td></tr>
';



$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';  

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong><br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
