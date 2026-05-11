<?php 
includeClass('CustomerDownpaymentSettlement.class.php');
$customerDownpaymentSettlement = createObjAndAddToCol( new CustomerDownpaymentSettlement()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 

$obj = $customerDownpaymentSettlement;
$generateReportContent = function ($dataset){ 

$obj = new CustomerDownpaymentSettlement();    
$customerDownpayment = $obj->getDownpaymentObj();
$chartOfAccount = new ChartOfAccount();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
 
if(!empty($rs[0]['coakey'])){
     $rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);
}
    
$decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;  

if($rs[0]['typekey'] == 1){
    $fieldPayment = 'totalpayment';
        $titleField = 'totalPayment';
}else{
    $fieldPayment = 'totalcoa';
    $titleField = 'total';
}
    
$periodeHTMTL = '';
if($rs[0]['usedateperiod'])
   $periodeHTMTL = '<tr><td class="header-row-header">Periode</td><td style="text-align:center">:</td><td>'. $obj->formatDBDate($rs[0]['startdateperiod']) .' - '.$obj->formatDBDate($rs[0]['enddateperiod']).'</td></tr>';
    
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PENYELESAIAN UANG MUKA PELANGGAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">Customer</td><td style="text-align:center">:</td><td>'. $rs[0]['customername'] .'</td></tr>    
'.$periodeHTMTL.'
</table> 
  
<div style="clear:both"></div> ';

$html .= '<table  cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '120'));
array_push($cellArray, array('label' => $obj->lang['refCode'] ));
array_push($cellArray, array('label' => $obj->lang['refDate'], 'align' => 'center', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['settlement'], 'align' => 'right', 'width' => '140'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
 
for ($i=0;$i<count($rsDetail);$i++){   
  $rsDPCustomer = $customerDownpayment->getDataRowById($rsDetail[$i]['downpaymentkey']);

  $refCode = array();
  if(!empty($rsDPCustomer[0]['refcode'])) array_push($refCode, $rsDPCustomer[0]['refcode']) ;
  $html .= '<tr><td>'.$rsDPCustomer[0]['code'].'</td><td>'.implode(', ',$refCode).'</td><td style="text-align:center">'. $obj->formatDBDate($rsDPCustomer[0]['trdate'],'',array('returnOnEmpty' => true, 'value' => '')) .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
}
$html .= '</table>' ;
     
$arrSubtotal = array(); 



    
if (!empty($arrSubtotal)){ 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
} 

    
    if ($rs[0][$fieldPayment] != 0) { 
         //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
         array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang[$titleField]).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0][$fieldPayment]).'</td></tr>'); 

        $balance = $rs[0][$fieldPayment]-$rs[0]['grandtotal'];
        if($balance <> 0)
         array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($balance).'</td></tr>');   
    }

    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['payingOffAmount']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
    
if($rs[0]['typekey'] == 1){
    $payment = '';

    $rsDPSettlementMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
    $payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';

    for ($j=0;$j<count($rsDPSettlementMethodDetail);$j++){  
    if ($rsDPSettlementMethodDetail[$j]['amount'] == 0) continue;
    $payment .= '<tr>';
    $payment .= '<td style="width: 150px;">'.$rsDPSettlementMethodDetail[$j]['paymentmethodname'].'</td>';
    $payment .= '<td style="text-align:center; width: 50px;">:</td>';
    $payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsDPSettlementMethodDetail[$j]['amount']).'</td>';
    $payment .= '</tr>'; 
    
    }
    $payment  .= '</table>'; 	

}else{
    $payment = '';

    $payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';

    $payment .= '<tr>';
    $payment .= '<td style="width: 150px;">'.$rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'].'</td>';
    $payment .= '<td style="text-align:center; width: 50px;">:</td>';
    $payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rs[0]['totalcoa'],$decimalPrice).'</td>';
    $payment .= '</tr>'; 
    
    $payment  .= '</table>'; 
}
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalreceived']).'</td></tr>
';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';  

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>