<?php 

includeClass('ARPayment.class.php');
$arPayment = createObjAndAddToCol( new ARPayment()); 

$obj = $arPayment;
$generateReportContent = function ($dataset){ 

$obj = new ARPAyment();  
$salesOrder = new SalesOrder();
$ar = new AR();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']);    
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PEMBAYARAN PIUTANG</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Pelanggan</td><td style="text-align:center">:</td><td>'. $rs[0]['customername'] .'</td></tr>    
</table> 
 
<div style="clear:both"></div> ';

$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['arCode'], 'width' => '100')); 
array_push($cellArray, array('label' => $obj->lang['reference'])); 
array_push($cellArray, array('label' => $obj->lang['refDate'], 'align' => 'center','width' => '80'));
array_push($cellArray, array('label' => $obj->lang['discount'],'align' => 'right', 'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['tax23'],'align' => 'right', 'width' => '70'));

$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 
 
for ($i=0;$i<count($rsDetail);$i++){   
    $rsAr = $ar->getDataRowById($rsDetail[$i]['arkey']);
    //  $rsSO = $salesOrder->getDataRowById($rsAr[0]['refheaderkey']);
    $refCode = array();
    array_push($refCode, $rsAr[0]['refcode']) ;
    array_push($refCode, $rsAr[0]['refcode2']) ;

  $html .= '<tr>
  <td>'.$rsAr[0]['code'].'</td><td>'.implode(', ', $refCode).'</td>  
  <td style="text-align:center">'. $obj->formatDBDate($rsAr[0]['refdate']) .'</td>
  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['discount']).'</td>
  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>
  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['taxamount']).'</td>
  </tr>' ; 
}
$html .= '</table>' ;
   
    
//$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

    
//here
    
$arrSubtotal = array(); 
 
if ($rs[0]['totaldiscount'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalDiscount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['totaldiscount'] * -1).'</td></tr>');
}
    

if ($rs[0]['prepaidtax23'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['tax23']).'</td><td style="text-align:right; font-weight:bold;" >'.$obj->formatNumber($rs[0]['prepaidtax23'] * -1).'</td></tr>');
}
    
if ($rs[0]['totaldownpayment'] > 0){ 
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment'] * -1).'</td></tr>'); 
} 
    
$rsAPCost = $obj->getCostDetail($rs[0]['pkey']);  
for ($j=0;$j<count($rsAPCost);$j++){  
 array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($rsAPCost[$j]['costname']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rsAPCost[$j]['amount'] * -1).'</td></tr>'); 
}
    
if (!empty($arrSubtotal)) { 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
}
    
if ($rs[0]['totalpayment'] != 0)  { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']).'</td></tr>'); 
     
    $balance = $rs[0]['totalpayment']-$rs[0]['grandtotal'];
    if($balance <> 0)
        array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($balance).'</td></tr>'); 
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$payment = '';
    
//bedain untuk metode pembayaran jika menggunakan voucher 
$rsARPaymentMethodDetail = (ADV_FINANCE && TEST_VOUCHER) ? $obj->getPaymentVoucherDetail($rs[0]['pkey'],'',2) : $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="2">';
  
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
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:450px;"><strong>Terbilang</strong><br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalreceived']).'</td></tr>
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
