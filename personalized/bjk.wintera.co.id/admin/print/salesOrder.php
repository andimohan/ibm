<?php 
$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'footer' => '', 
         'showPrintHeader' => false,
         ) 
); 

$obj = $salesOrder;
$generateReportContent = function ($dataset){ 
$obj = new SalesOrder();  

    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);   
    
$arrRecipient = array();
     
if(!empty($rs[0]['recipientname'])){
     array_push($arrRecipient, $rs[0]['recipientname']);
     if (!empty($rs[0]['recipientaddress'])) array_push($arrRecipient, str_replace(chr(13),'<br>',$rs[0]['recipientaddress'])); 
     if (!empty($rs[0]['recipientphone'])) array_push($arrRecipient, $rs[0]['recipientphone']); 
}else{
    $customer = new Customer();    
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    array_push($arrRecipient, $rsCustomer[0]['name']);
     if (!empty($rsCustomer[0]['address'])) array_push($arrRecipient, str_replace(chr(13),'<br>',$rsCustomer[0]['address'])); 
     if (!empty($rsCustomer[0]['phone'])) array_push($arrRecipient, $rsCustomer[0]['phone']); 
     if (!empty($rsCustomer[0]['mobile'])) array_push($arrRecipient, $rsCustomer[0]['mobile']); 
}
 
    
$html = $obj->printSetting['defaultStyle'];
$html .= '<div style="clear:both"></div>';
$html .= '<table>
    <tr>
        <td style="width:325px">
        <table cellpadding="2"> 
            <tr><td class="header-row-header" colspan = "2" style="width:375px">TOKO BANDARA JAYA</td></tr>
            <tr><td colspan = "2"></td></tr>
            <tr><td class="header-row-header" style="width:70px;">No. Faktur</td><td style="width:10">:</td><td>'.$rs[0]['code'].'</td></tr> 
        </table>
        </td>
        <td style="width:350px"><table cellpadding="2"> 
            <tr><td class="header-row-header" style="width:100px">'.ucwords($obj->lang['date']).'</td><td style="width:10">:</td><td style="width:240px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
            <tr><td><strong>Kepada Yth.</strong></td><td>:</td><td>'.implode('<br>',$arrRecipient).'</td></tr> 
            </table>  
        </td>
    </tr> 
</table>
<div style="clear:both;"></div>';

$html .= '<table cellpadding="2" class="table-transaction">
<tr class="col-header"><td style="width:40px;text-align:right" >No.</td><td style="width:100px;text-align:center" >Jumlah</td><td style="width:70px;">Kode</td><td style="width:220px;">Nama Barang</td><td style="width:80px;text-align:right" >Harga @</td><td style="width:60px;text-align:right" >Diskon @</td><td style="text-align:right; width:100px;">Subtotal</td></tr>'; 

for ($i=0;$i<count($rsDetail);$i++){  

if ($rsDetail[$i]['discounttype'] == 2)
    $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
    
  $html .= '<tr><td style="text-align:right">'.($i+1).'</td><td style="text-align:center">'.$obj->formatNumber($rsDetail[$i]['qty']).' '. $rsDetail[$i]['unitname'] .'</td><td>'.$rsDetail[$i]['itemcode'].'</td><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['discount']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
}
$html .= '</table>';
    
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
   
    
$html .= '<table cellpadding="2" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:90px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div>';
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Hormat Kami'));
array_push($arrSignLabel, array('Telah Diterima'));

$html .=' 
    <table cellpadding="4" class="sign">
    <tr>'; 
    for ($i=0;$i<count($arrSignLabel);$i++){
        $html .='<td  class="sign-col" style="height:50px"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
        if ($i <> count($arrSignLabel) - 1)
            $html .= '<td class="sign-col-space"></td>';
    }
    $html .='</tr>  
    </table>' ;


return '<div style="font-size:1em">'.$html.'</div>';
}
?>
