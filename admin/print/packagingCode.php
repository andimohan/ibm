<?php  
// sementara, contentnya masih salah
// cuma biar bisa muncul di personalized saja dulu


includeClass(array('ReceivingPurchaseJewelry.class.php','PackagingCode.class.php'));

includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 

$obj = $salesOrder;
$generateReportContent = function ($dataset){ 
$obj = new SalesOrder(); 
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
    
$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">ORDER PENJUALAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td >
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width: 100px;">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width: 560px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td colspan="3" class="header-row-header"></td></tr> 
<tr><td colspan="3" class="header-row-header">Kepada Yth.</td></tr> 
<tr><td colspan="3"  style="width: 670px;">'.implode('<br>',$arrRecipient).'</td></tr>  
</table> 
</td>
<td style="text-align:right"><img src="'.$obj->createQR($rs[0]['code'],3)['url'].'" /></td>
</tr>
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:280px;">Item</td><td style="width:70px;text-align:right" >Jumlah</td><td style="width:60px;" >Unit</td><td style="width:80px;text-align:right" >Harga @</td><td style="width:80px;text-align:right" >Diskon @</td><td style="text-align:right; width:110px;">Subtotal</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

if ($rsDetail[$i]['discounttype'] == 2)
    $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
    
  $html .= '<tr><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td><td>'. $rsDetail[$i]['unitname'] .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['discount']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
}
$html .= '</table>' ;

$html .= '<div style="clear:both"></div>';
        
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
 
    
$arrSubtotal = array(); 
    
     
if ($rs[0]['finaldiscount'] != 0){
  
   $rs[0]['finaldiscount']  = $obj->getDiscountValue($rs[0]['subtotal'], $rs[0]['finaldiscount'],$rs[0]['finaldiscounttype']); 
   $rs[0]['finaldiscount'] *= -1;
	
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}    


if ($rs[0]['finaldiscount2'] != 0){
	 
	// + , karena sudah negatif diatas
   	$rs[0]['finaldiscount2']  = $obj->getDiscountValue( ($rs[0]['subtotal'] + $rs[0]['finaldiscount']) , $rs[0]['finaldiscount2'],$rs[0]['finaldiscounttype2']); 
	$rs[0]['finaldiscount2'] *= -1;

	array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).' 2</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount2']).'</td></tr>');
}  

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">DPP</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');

}   
    
if ( $rs[0]['shipmentfee']!= 0){
    $shipment = new Shipment();
    $rsShipment = $shipment->getDataRowById($rs[0]['shipmentkey']);
    $recipientCourier = $rsShipment[0]['name'];
    if($rs[0]['useinsurance'] == 1)
        $recipientCourier .= '. Asuransi'; 
    
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Ongkos Kirim<div style="font-weight:normal">'.$recipientCourier.'</div></td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['shipmentfee']).'</td></tr>');

}   
    
if ( $rs[0]['etccost'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Biaya Lain</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['etccost']).'</td></tr>');
}   
     
if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>'.$obj->lang['termofpayment'].' :</strong> '.$topSaid.'<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '</table>
<div style="clear:both"></div>'; 
$html .= $obj->loadSetting('invoiceFooter');
	
$html .= '<div style="clear:both"></div>';
$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>