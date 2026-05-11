<?php 

$generateReportContent = function ($dataset){ 

$obj = new APEmployeeCommissionPayment();    
$apEmployeeCommission = $obj->getAPObj();
    
$car = new Car();
$location = new Location();


$truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
 
$datePeriod = ($rs[0]['usedateperiod'] == 1)  ? $obj->formatDBDate($rs[0]['startdateperiod']) . ' - ' . $obj->formatDBDate($rs[0]['enddateperiod']) : '-';
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2"> 
<tr><td><div class="title">PEMBAYARAN HUTANG KOMISI SUPIR</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate']).'</td></tr>  
<tr><td class="header-row-header">Karyawan</td><td style="text-align:center">:</td><td>'. $rs[0]['employeename'] .'</td></tr>   
<tr><td class="header-row-header">Periode</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$datePeriod.'</td></tr>  
</table> 

<div style="clear:both"></div> 

<table  cellpadding="4" class="table-transaction">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
array_push($cellArray, array('label' => $obj->lang['ap'],  'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['refCode'], 'width' => '130')); 
array_push($cellArray, array('label' => $obj->lang['carRegistrationNumber'], 'width'=>'100'));
array_push($cellArray, array('label' => $obj->lang['location'], 'width'=>'90'));
array_push($cellArray, array('label' => $obj->lang['stuffingAndDestuffingDateTime'], 'width'=>'120'));
array_push($cellArray, array('label' => $obj->lang['amount'], 'width'=>'100', 'align'=> 'right'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  

$no = 0;
for ($i=0;$i<count($rsDetail);$i++){   
  $rsAp = $apEmployeeCommission->getDataRowById($rsDetail[$i]['apkey']);
/*  $rsKey = $ap->getTableKeyAndObj($rsDetail[$i]['reftabletype']);  
  $purchaseOrder = $rsKey['obj'];
  $rsPO = $purchaseOrder->getDataRowById($rsAp[0]['refheaderkey']);*/
    $rsWO = $truckingServiceWorkOrder->getDataRowById($rsAp [0]['refkey']);
    
    $timeformat = ($obj->formatDBDate($rsWO[0]['stuffingdatetime'],'H:i') == "00:00") ? 'd / m / Y' : 'd / m / Y H:i';     
    
    $rsCar = $car->getDataRowById($rsWO[0]['carkey']);
    $carRegistrationNumber = (!empty($rsCar[0]['policenumber'])) ? ''.$rsCar[0]['policenumber'].'' : '';
    
    $rsLocation = $location->getDataRowById($rsWO[0]['locationkey']);
    $locationname = (isset($rsLocation[0]['name'])) ? $rsLocation[0]['name'] : '';

  $refCode = array(); 
  if(!empty($rsAp[0]['refcode'])) array_push($refCode, $rsAp[0]['refcode']) ;
  if(!empty($rsAp[0]['refcode2'])) array_push($refCode, $rsAp[0]['refcode2']) ;
  $html .= '<tr><td style="text-align:right">'.++$no.'</td><td>'.$rsAp[0]['code'].'</td><td>'.implode('<br>',$refCode).'</td><td>'.$carRegistrationNumber.'</td><td>'.$locationname.'</td><td style="text-align:center">'. $obj->formatDBDate($rsWO[0]['stuffingdatetime'],$timeformat,array('returnOnEmpty' => true, 'value' => '')) .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
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
    
if (!empty($arrSubtotal)) { 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
} 
    
if ($rs[0]['totalpayment'] != 0)  { 
     //array_push($arrSubtotal, '<tr><td></td><td></td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['totalPayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totalpayment']).'</td></tr>'); 
     array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['balance']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['totalpayment'])).'</td></tr>'); 
    
}
    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$payment = '';
$rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';
  
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
$payment .= '<tr>';
$payment .= '<td style="width: 150px;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
$payment .= '<td style="text-align:center; width: 50px;">:</td>';
$payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
$payment .= '</tr>'; 
}

$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:440px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalpaid']).'</td></tr>
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