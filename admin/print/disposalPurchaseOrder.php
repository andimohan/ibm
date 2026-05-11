<?php 
 $PRINT_SETTINGS =  array(   
    'showPrintHeader' => false,
    'showPrintFooter' => false,
);
includeClass('DisposalPurchaseOrder.class.php');
$disposalPurchaseOrder = createObjAndAddToCol( new DisposalPurchaseOrder()); 

$obj = $disposalPurchaseOrder;
$generateReportContent = function ($dataset){ 

$obj = new DisposalPurchaseOrder();  
$supplier = new Supplier();
$termOfPayment = new TermOfPayment();
$city = new City();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

$arrRecipient = array();
array_push($arrRecipient, $rsSupplier[0]['name']);
if (!empty($rsSupplier[0]['address1'])) array_push($arrRecipient, $rsSupplier[0]['address1']);
if (!empty($rsSupplier[0]['address2'])) array_push($arrRecipient, $rsSupplier[0]['address2']); 
    
if (!empty($rsSupplier[0]['citykey'])){
    $rsCity = $city->searchData('city.pkey',$rsSupplier[0]['citykey'],true);
    $cityname = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
    
    array_push($arrRecipient, $cityname); 
}
if (!empty($rsSupplier[0]['zipcode'])) array_push($arrRecipient, $rsSupplier[0]['zipcode']); 
if (!empty($rsSupplier[0]['phone'])) array_push($arrRecipient, $rsSupplier[0]['phone']); 
if (!empty($rsSupplier[0]['mobile'])) array_push($arrRecipient, $rsSupplier[0]['mobile']); 
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">ORDER PEMBELIAN</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width: 500px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
</table> 

<div style="clear:both"></div>

<table cellpadding="2"> 
<tr><td><strong>Pengolah</strong></td></tr>
<tr><td>'.implode('<br>',$arrRecipient).'</td></tr>
</table>
  
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:340px;">Limbah</td><td style="width:110px;text-align:right" >Berat (Kg)</td><td style="width:120px;text-align:right" >Harga @</td><td style="text-align:right; width:110px;">Subtotal</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

if ($rsDetail[$i]['discounttype'] == 2)
    $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
    
  $html .= '<tr><td>'.$rsDetail[$i]['waste'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['weightdetail'], 2).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
}
$html .= '</table>' ;

$html .= '<div style="clear:both"></div>';
 
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$arrSubtotal = array(); 
    

if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}    
if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">DPP</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
}   
    
if ( $rs[0]['shipmentfee']!= 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Ongkos Kirim</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['shipmentfee']).'</td></tr>');
   
}   
    
if ( $rs[0]['etccost'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Biaya Lain</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['etccost']).'</td></tr>');
}   
 

if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 

    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
     
$html .= '<table cellpadding="4"> 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>'.$obj->lang['termofpayment'].' :</strong> '.$topSaid.'<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
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