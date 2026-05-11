<?php 
includeClass(array('EMKLPurchaseOrder.class.php'));
$emklPurchaseOrderDomestic = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['domestic']));

$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         );
   
$obj = $emklPurchaseOrderDomestic;
 
$generateReportContent = function ($dataset){ 
 
$obj = new EMKLPurchaseOrder(EMKL['jobType']['domestic']);  
$emklJobOrderDomestic = new EMKLJobOrder(EMKL['jobType']['domestic']);  
$emklJobOrderHeaderDomestic = new EMKLJobOrderHeader(EMKL['jobType']['domestic']);
    
$rsJOType = $obj->getTableKeyAndObj($emklJobOrderDomestic->tableName,array('key'));
    
$rs = $dataset['rs'];  
$supplier = new Supplier();
$rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.name'),
									  ' and '.$supplier->tableName.'.pkey = ' .  $supplier->oDbCon->paramString($rs[0]['supplierkey'])
									  );
	
if($rs[0]['currencykey'] == CURRENCY['idr']){ 
    $currencyName = 'IDR';
    $sayCurrencyName = 'Rupiah';
	$headerDecimal = 0;
}else{
	$currencyName = 'USD';
	$sayCurrencyName = 'USD'; 
	$headerDecimal = 2;
}
    
//$currencyName = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 'IDR' : 'USD' ;

if($rs[0]['reftabletype']==$rsJOType['key']){
    $rsEmkl = $emklJobOrderDomestic->searchData($emklJobOrderDomestic->tableName.'.pkey',$rs[0]['refkey']);
    $joCode = $rsEmkl[0]['code'];
    $stuffing = $rsEmkl[0]['stuffinglocation'];
    
}else{
    $rsEmkl = $emklJobOrderHeaderDomestic->searchData($emklJobOrderHeaderDomestic->tableName.'.pkey',$rs[0]['refjoheaderkey']); 
    $joCode =  $rsEmkl[0]['code'];
    $stuffing = $rsEmkl[0]['stuffing'];

}
        $mblNumber = (!empty($rsEmkl[0]['bookingnumber'])) ? $rsEmkl[0]['bookingnumber'] : $rsEmkl[0]['mblnumber'];

$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>'.$obj->lang['note'].' :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
  
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">Purchase Order Domestic</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header">'.$obj->lang['transactionDate'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'. $obj->formatDBDate($rs[0]['trdate']) .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['jobOrder'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$joCode.'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['poReference'].'</td><td style="text-align:center">:</td><td>'.$rsEmkl[0]['ponumber'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['invoiceReference'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['refinvoicecode'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['supplier'].'</td><td style="text-align:center">:</td><td>'.$rsSupplier[0]['name'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['bookingNumber'].'</td><td style="text-align:center">:</td><td>'.$rsEmkl[0]['bookingnumber'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['typeOfJob'].'</td><td style="text-align:center">:</td><td>'.$rsEmkl[0]['jobtypeunion'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['currency'].' / '.$obj->lang['rate'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['currencyname'].' / '.$obj->formatNumber($rs[0]['rate'],-2).'</td></tr>
</table>
</td>
<td style="width:10px;"></td>
<td style="width:360px;"> 
<table cellpadding="2">
<tr><td class="header-row-header">MBL</td><td style="width:10px; text-align:center">:</td><td style="width:230px">'.$mblNumber.'</td></tr> 
<tr><td class="header-row-header">POL, ETD</td><td style="text-align:center">:</td><td >'. $rsEmkl[0]['polname'] .', '. $obj->formatDBDate($rsEmkl[0]['etdpol']) .'</td></tr>
<tr><td class="header-row-header">POD, ETA</td><td style="text-align:center">:</td><td >'. $rsEmkl[0]['podname'] .', '. $obj->formatDBDate($rsEmkl[0]['etapod']) .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['shipper'].'</td><td style="text-align:center">:</td><td>'.$rsEmkl[0]['customername'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['terminal'].' / '.$obj->lang['depot'].'</td><td style="text-align:center">:</td><td>'.$rsEmkl[0]['terminalname'].' / '.$rsEmkl[0]['depotname'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['container'].'</td><td style="text-align:center">:</td><td>'.str_replacE(chr(13),'<br>',$rsEmkl[0]['containernumber']).'</td></tr>
<tr><td class="header-row-header">Stuffing Location</td><td style="text-align:center">:</td><td>'.$stuffing.'</td></tr>
</table>
</td>
</tr>
</table>
    
<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['container'],  'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right',  'width' => '60' ));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['curr'], 'align' => 'center', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'],'align' => 'right',  'width' => '90' ));
//array_push($cellArray, array('label' => $obj->lang['total'], 'align' => 'right', 'width' => '100'));
array_push($cellArray, array('label' => $obj->lang['subtotal'] . ' ('.$rs[0]['currencyname'].')', 'align' => 'right', 'width' => '100'));
  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

for($i=0;$i<count($rsDetail);$i++){  
    $detailDecimal = ($rsDetail[$i]['currencykey'] == 1) ? 0 : 2;   
    $html .= '<tr>
				<td>'.$rsDetail[$i]['containername'].'</td>
				<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty'],-2).'</td> 
				<td>'.$rsDetail[$i]['servicename'].'</td>
				<td style ="text-align:center">'.$rsDetail[$i]['currencyname'].'</td>
				<td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit'],$detailDecimal).'</td> 
				<td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['subtotal'],$headerDecimal).'</td>
			  </tr>';

	// <td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['subtotalcurrency'],-2).'</td>
	
} 


$html .= '</table><div style="clear:both"></div>';

$sayNumber = $obj->sayNumber($obj->unFormatNumber($obj->formatNumber($rs[0]['grandtotal'],$headerDecimal)));
    
$arrSubtotal = array(); 
    

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">DPP</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal'],$headerDecimal).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue'],$headerDecimal).'</td></tr>');

}   
     
if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal'],$headerDecimal).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$payment = '';
$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
$payment .= '<br><strong>'.$obj->lang['paymentMethod'].'</strong><br><table cellpadding="4">';
  
for ($j=0;$j<count($rsPaymentMethodDetail);$j++){  
if ($rsPaymentMethodDetail[$j]['amount'] == 0) continue;
$payment .= '<tr>';
$payment .= '<td style="width: 150px;">'.$rsPaymentMethodDetail[$j]['paymentmethodname'].'</td>';
$payment .= '<td style="text-align:center; width: 50px;">:</td>';
$payment .= '<td style="text-align:right; width: 80px;">'.$obj->formatNumber($rsPaymentMethodDetail[$j]['amount'],$headerDecimal).'</td>';
$payment .= '</tr>'; 
}

$payment  .= '</table>'; 	
    
        
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' '.$sayCurrencyName.'.<br>'.$payment.'<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$obj->formatNumber($rs[0]['subtotal'],$headerDecimal).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
          
return $html;
}
?>
