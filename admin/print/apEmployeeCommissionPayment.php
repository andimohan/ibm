<?php 

includeClass(array('APEmployeeCommissionPayment.class.php','Car.class.php','TruckingServiceWorkOrder.class.php'));
$apEmployeeCommissionPayment = createObjAndAddToCol( new APEmployeeCommissionPayment()); 

$obj = $apEmployeeCommissionPayment;
$generateReportContent = function ($dataset){ 

$obj = new APEmployeeCommissionPayment();    
$apEmployeeCommission = $obj->getAPObj();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
 
$datePeriod = ($rs[0]['usedateperiod'] == 1)  ? $obj->formatDBDate($rs[0]['startdateperiod']) . ' - ' . $obj->formatDBDate($rs[0]['enddateperiod']) : '-';
 
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PEMBAYARAN HUTANG KOMISI KARYAWAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate']).'</td></tr>  
<tr><td class="header-row-header">Karyawan</td><td style="text-align:center">:</td><td>'. $rs[0]['employeename'] .'</td></tr>    
<tr><td class="header-row-header">Periode</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$datePeriod.'</td></tr>  
</table> 
  
<div style="clear:both"></div> ';

$html .= '<table  cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['ap'], 'width' => '150'));
array_push($cellArray, array('label' => $obj->lang['reference'], 'width' => '195'));
array_push($cellArray, array('label' => $obj->lang['date'],'align' => 'center', 'width' => '85'));
	
if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking'])
	array_push($cellArray, array('label' => $obj->lang['consignee'])); 
	
array_push($cellArray, array('label' => $obj->lang['amount'] , 'align' => 'right', 'width' => '100'));    
	
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '680', 'cell' =>  $cellArray));  
	
$arrAPKeys = array_column($rsDetail,'apkey');
$rsAPCol = $apEmployeeCommission->searchData('','',true, ' and '.$apEmployeeCommission->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrAPKeys,',').') ');

$rsAPCol = array_column($rsAPCol,null,'pkey');
	
for ($i=0;$i<count($rsDetail);$i++){   
  $rsAp = $rsAPCol[$rsDetail[$i]['apkey']] ; //$apEmployeeCommission->getDataRowById($rsDetail[$i]['apkey']);
 
  $refCode = array(); 
  if(!empty($rsAp['refcode'])) array_push($refCode, $rsAp['refcode']) ;
  if(!empty($rsAp['refcode2'])) array_push($refCode, $rsAp['refcode2']) ;
  $html .= '<tr><td>'.$rsAp['code'].'</td><td>'.implode(', ',$refCode).'</td>
  				<td style="text-align:center">'. $obj->formatDBDate($rsAp['refdate'],'',array('returnOnEmpty' => true, 'value' => '')) .'</td>';
	
  if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking'])
		  $html .= '<td>'. $rsAp['consigneename'] .'</td>';
	
 $html .= '<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>
			</tr>' ; 
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
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="2">';
  
for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){  
$payment .= '<tr>';
$payment .= '<td style="width: 120px;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
$payment .= '<td style="text-align:center; width: 15px;">:</td>';
$payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount']).'</td>';
$payment .= '</tr>'; 
}

$payment  .= '</table>'; 	
    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:450px;"><strong>Terbilang</strong><br>'.ucwords($sayNumber).' Rupiah. <br>'.$payment.'</td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['totalpaid']).'</td></tr>
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
