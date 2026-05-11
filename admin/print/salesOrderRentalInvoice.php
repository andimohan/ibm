<?php  
$obj = $salesOrderRentalInvoice;

$generateReportContent = function ($dataset){ 
$obj = new SalesOrderRentalInvoice();  
$salesOrderRental = new SalesOrderRental();    
$customer = new Customer();    
//$rig = new Rig();    
$location = new Location();    
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']); 
$rsSORental = $salesOrderRental->getDataRowById($rs[0]['refkey']); 
        
//$rsRig = $rig->getDataRowById($rsSORental[0]['rigkey']); 
//$rsLocation = $location->getDataRowById($rsRig[0]['locationkey']); 
$rsLocation = $location->getDataRowById($rsSORental[0]['locationkey']); 

array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);

$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];    

$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=260&h=130&hash='.getPHPThumbHash($profileImg);

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">PORFORMA INVOICE</div></td></tr>
<tr><td><div class="subtitle">RINCIAN PEMBAYARAN</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td >
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width: 100px;">Nomor</td><td style="width:10px; text-align:center">:</td><td style="width: 460px;">'.$rs[0]['code'].'</td><td rowspan="5"><img src="'.$img.'"></td></tr>  
<tr><td class="header-row-header" style="width: 100px;">Lokasi</td><td style="width:10px; text-align:center">:</td><td style="width: 460px;">'.$rsLocation[0]['name'].'</td></tr> 
<tr><td class="header-row-header" style="width: 100px;">No Kontrak</td><td style="width:10px; text-align:center">:</td><td style="width: 460px;">'.$rsSORental[0]['code'].'</td></tr> 
<tr><td class="header-row-header" style="width: 100px;">Periode</td><td style="width:10px; text-align:center">:</td><td style="width: 460px;">'.$topSaid.'</td></tr> 

</table> 
</td>
<td></td>
</tr>
<div style="clear:both"></div> ';
    
$cellArray = array();
array_push($cellArray, array('label' => 'No', 'width' => '30', 'align' => 'right'));
array_push($cellArray, array('label' => 'Deskripsi'));
array_push($cellArray, array('label' => 'Jumlah', 'width' => '100','align' => 'right'));
array_push($cellArray, array('label' => 'Unit', 'width' => '50'));
array_push($cellArray, array('label' => 'Total hari', 'width' => '90','align' => 'center'));
array_push($cellArray, array('label' => 'Rupiah/Hari', 'width' => '80','align' => 'right'));
array_push($cellArray, array('label' => 'Total (Rp)', 'width' => '100','align' => 'right'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));
    $no = 0;

for ($i=0;$i<count($rsDetail);$i++){  
if ($rsDetail[$i]['discounttype'] == 2)
    $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
    
  $html .= '<tr>
                <td style="text-align:right">'.(++$no).'</td>
                <td>'.$rsDetail[$i]['itemname'].'</td>
                <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
                <td >'. $rsDetail[$i]['unitname'] .'</td>
                <td style="text-align:center">'.$obj->formatNumber($rsDetail[$i]['totaldays']).'</td>
                <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>
                <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
            </tr>' ; 
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
    
    
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div>';

//$html .= $obj->generateSignLabel($rs); 
    
$html .= '<table>
<tr><td style="width: 400px; text-align:center;"></td><td style="height:120px;text-align:center;">'.$obj->formatDBDate($rs[0]['trdate'],'d F Y').' <br> PT. BINAKARINDO YACOAGUNG</td></tr>
<tr><td></td><td style="text-align:center;"><strong>Rianto Kristanto</strong></td></tr>

</table>';
    
return $html;
}
?>
