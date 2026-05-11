<?php 
 
$generateReportContent = function ($dataset){ 

$obj = createObjAndAddToCol(new APPayment());    
$emklPurchaseOrderExport = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['export']));
$emklCommission = createObjAndAddToCol(new EMKLCommission());                                                 
                                                 
$ap = $obj->getAPObj();
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());

$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 

$decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;    

$EMKLPoObj = $obj->getTableKeyAndObj($emklPurchaseOrderExport->tableName);
$EMKLComObj = $obj->getTableKeyAndObj($emklCommission->tableName);

$arrEMKLObj = array(); 
$arrEMKLObj[$EMKLPoObj['key']] = $EMKLPoObj['obj'];
$arrEMKLObj[$EMKLComObj['key']] = $EMKLComObj['obj']; 
$arrEMKLObjKey = array_keys($arrEMKLObj); 
$rate = ($rs[0]['currencykey'] == CURRENCY['idr']) ? 1 : $rs[0]['rate'] ;

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
<tr><td class="header-row-header">Rate</td><td style="text-align:center">:</td><td>'. $obj->formatNumber($rate,-2) .'</td></tr>    
</table> 
  
<div style="clear:both"></div> ';

$html .= '<table  cellpadding="4" class="table-transaction">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '90'));
array_push($cellArray, array('label' => $obj->lang['refCode'], 'width' => '120' ));
array_push($cellArray, array('label' => $obj->lang['etd'], 'align' => 'center', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['shipper']));
array_push($cellArray, array('label' => $obj->lang['total'] . ' ('.$rs[0]['currencyname'].')', 'align' => 'right', 'width' => '100'));
if($rs[0]['currencykey'] != CURRENCY['idr'])
    array_push($cellArray, array('label' => $obj->lang['total'] . ' (IDR)', 'align' => 'right', 'width' => '100'));
    
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

 
for ($i=0;$i<count($rsDetail);$i++){   
  $rsAp = $ap->getDataRowById($rsDetail[$i]['apkey']);
    
    if(in_array($rsAp[0]['reftabletype'],$arrEMKLObjKey)){

       $reftabletype = $rsAp[0]['reftabletype'];
       $refheaderkey = $rsAp[0]['refheaderkey'];
 
       $refObj = $arrEMKLObj[$reftabletype];

       // ambil data dari PO / Refund utk dapetin refkey agar bisa link ke JO
       $rsObj = $refObj->getDataRowById($refheaderkey);  
       $rsJO = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rsObj[0]['refkey'],true);
        
       $rsAp[0]['etd'] = $rsJO[0]['etdpol']; 
       $shipperName = $rsJO[0]['customername']; 
   }
   
    
  $totalIdr = $rsDetail[$i]['amount'] * $rate;    
    
  $refCode = array();
  array_push($refCode, $rsAp[0]['refcode2']) ;
  array_push($refCode, $rsAp[0]['refcode']) ;
  $html .= '<tr><td>'.$rsAp[0]['code'].'</td><td>'.implode(', ',$refCode).'</td><td style="text-align:center">'. $obj->formatDBDate($rsAp[0]['etd']) .'</td><td>'. $shipperName.'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount'],$decimalPrice).'</td>';

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
    
if (!empty($arrSubtotal)) { 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']* $rate,$decimalPrice).'</td></tr>'); 
} 
    
if ($rs[0]['totalpayment'] != 0)  { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']* $rate,$decimalPrice).'</td></tr>'); 
    
     $balance = $rs[0]['totalpayment']-$rs[0]['grandtotal'];
     if($balance <> 0)
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($balance* $rate,$decimalPrice).'</td></tr>'); 
    
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']* $rate);
    
$payment = '';
$rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';
  
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
if ($rsARPaymentMethodDetail[$j]['amount'] == 0) continue;
    
$payment .= '<tr>';
$payment .= '<td style="width: 150px;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
$payment .= '<td style="text-align:center; width: 50px;">:</td>';
$payment .= '<td style="text-align:right; width: 100px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']* $rate,$decimalPrice).'</td>';
$payment .= '</tr>'; 
}

$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).'<br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalpaid']* $rate,$decimalPrice).'</td></tr>
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
