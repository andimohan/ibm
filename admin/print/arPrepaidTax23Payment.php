<?php 
$obj = $arPrepaidTax23Payment;
$generateReportContent = function ($dataset){ 

$obj = new ARPrepaidTax23Payment();    
$arPrepaidTax = $obj->getARObj();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">BUKTI PENERIMAAN PPH 23</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Pelanggan</td><td style="text-align:center">:</td><td>'. $rs[0]['customername'] .'</td></tr>    
</table> 
  
<div style="clear:both"></div> ';

$html .= '<table  cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['refCode'] ));
array_push($cellArray, array('label' => $obj->lang['refDate'], 'align' => 'center', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['amount'], 'align' => 'right', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['outstanding'], 'align' => 'right', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['paymentAmount'], 'align' => 'right', 'width' => '120'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
 
for ($i=0;$i<count($rsDetail);$i++){   
  $rsArPrepaidTax = $arPrepaidTax->getDataRowById($rsDetail[$i]['arkey']);
/*  $rsKey = $ap->getTableKeyAndObj($rsDetail[$i]['reftabletype']);   
  $purchaseOrder = $rsKey['obj'];
  $rsPO = $purchaseOrder->getDataRowById($rsAp[0]['refheaderkey']);*/
    
  $refCode = array();
  if(!empty($rsArPrepaidTax[0]['refcode'])) array_push($refCode, $rsArPrepaidTax[0]['refcode']) ;
  if(!empty($rsArPrepaidTax[0]['refcode2'])) array_push($refCode, $rsArPrepaidTax[0]['refcode2']) ;
  $html .= '<tr><td>'.$rsArPrepaidTax[0]['code'].'</td><td>'.implode(', ',$refCode).'</td><td style="text-align:center">'. $obj->formatDBDate($rsArPrepaidTax[0]['refdate'],'',array('returnOnEmpty' => true, 'value' => '')) .'</td><td style="text-align:right">'.$obj->formatNumber($rsArPrepaidTax[0]['amount']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['outstanding']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
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
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['totalpayment'])).'</td></tr>');   
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['payingOffAmount']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
//$payment = '';
//$rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
//$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';
//  
//for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
//if ($rsARPaymentMethodDetail[$j]['amount'] == 0) continue;
//$payment .= '<tr>';
//$payment .= '<td style="width: 150px;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
//$payment .= '<td style="text-align:center; width: 50px;">:</td>';
//$payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
//$payment .= '</tr>'; 
//}
//
//$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah. <br></td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalreceived']).'</td></tr>
';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';  

//$html .= '
//<table cellpadding="4">
//<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
//</table>
$html .= '<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>