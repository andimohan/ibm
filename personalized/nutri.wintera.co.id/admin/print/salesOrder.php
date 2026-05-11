<?php  
$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false,
         'showPrintFooter' => false,
         'footer' => '',  
         ) 
);
$generateReportContent = function ($dataset){ 
    
global $pdf;
$obj = new SalesOrder(); 
$termOfPayment = new TermOfPayment();
$customer = new Customer();
$employee = new Employee();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsSales = $employee->getDataRowById($rs[0]['saleskey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$duedate = date('Y-m-d', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));
    
$salesname = $rsSales[0]['name'];

$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);

    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">FAKTUR PENJUALAN</div></td></tr>
<tr><td><div class="subtitle"></div></td></tr>
</table> 

<table cellpadding=""> 
<tr><td style="width:400px" rowspan="4">Kepada Yth, <br>'.implode('<br>',$arrRecipient).' </td><td style="width:100px">SALES</td><td style="width:10px">:</td><td>'.$salesname.'</td></tr>   
<tr><td style="width:100px">Tanggal</td><td style="width:10px">:</td><td>'.$obj->formatDBDate($rs[0]['trdate']).'</td></tr>   
<tr><td style="width:100px">J.Tempo</td><td style="width:10px">:</td><td>'.$obj->formatDBDate($duedate).'</td></tr>   
<tr><td style="width:100px">No. Faktur</td><td style="width:10px">:</td><td>'.$rs[0]['code'].'</td></tr>   
</table> ';

$html .= ' 
<table  cellpadding="2" class="table-transaction">
<tr class="col-header" ><td style="width:90px;">Kode</td><td style="width:190px;">Nama Barang</td><td style="width:70px;text-align:center" >Qty</td><td style="width:70px;text-align:right" >Harga</td><td style="width:70px;text-align:right" >Disc</td><td style="text-align:right; width:70px;">Netto</td><td style="text-align:right; width:110px;">Nilai</td></tr>';
$qtyTotal = 0;
for ($i=0;$i<count($rsDetail);$i++){  

if ($rsDetail[$i]['discounttype'] == 2)
    $rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
    $qtyTotal += $obj->formatNumber($rsDetail[$i]['qty']);
  $html .= '<tr><td>'.$rsDetail[$i]['itemcode'].'</td><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:center">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['discount']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
}
$html .= '</table>' ;
        
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
 
    
$arrSubtotal = array(); 
    
     
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="width:20px">:</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}    

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">DPP</td><td style="width:20px">:</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">Pajak</td><td style="width:20px">:</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');

}   
    
if ( $rs[0]['shipmentfee']!= 0){
    $shipment = new Shipment();
    $rsShipment = $shipment->getDataRowById($rs[0]['shipmentkey']);
    $recipientCourier = $rsShipment[0]['name'];
    if($rs[0]['useinsurance'] == 1)
        $recipientCourier .= '. Asuransi'; 
    
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">Ongkos Kirim</td><td style="width:20px">:</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['shipmentfee']).'</td></tr>');

}   
    
if ( $rs[0]['etccost'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">Biaya Lain</td><td style="width:20px">:</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['etccost']).'</td></tr>');
}   
     
if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style="text-align:left; font-weight:bold;">Total</td><td style="width:20px">:</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$tbQtyTOtal = '<table >
<tr><td style="width:60px">Total</td><td style="width:20px;">:</td><td style="width:300px">'.$obj->formatNumber($qtyTotal,2).'</td></tr>
<tr><td style="width:60px">Terbilang</td><td style="width:20px;">:</td><td style="width:300px">'.ucwords($sayNumber).' Rupiah.</td></tr>
</table>';
    
    
$html .= '<table cellpadding="2" style="border-bottom:1px solid black"> 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:440px">'.$tbQtyTOtal.'</td>
<td style="text-align:left; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="width:20px">:</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>';

$html .= '<table cellpadding="4">
<tr><td style="width:70px">Keterangan</td><td style="width:20px">:</td><td style="width:400px">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
<tr><td>Note</td><td>:</td><td>'.$obj->loadSetting('emailInvoiceFooter').'</td></tr>
<tr colspan="3">
    <td>
        <table>
              <tr>
                <td style="text-align:center;width:100px;height:70px;border-bottom:solid 1px black">Penerima</td>
                <td style="width:40px"></td><td  style="text-align:center;width:100px;border-bottom:solid 1px black">Pengirim</td>
                <td style="width:40px"></td><td  style="text-align:center;width:100px;border-bottom:solid 1px black">Gudang</td>
                <td style="width:40px"></td><td  style="text-align:center;width:100px;border-bottom:solid 1px black">Accounting</td>
                <td style="width:40px"></td><td  style="text-align:center;width:100px;border-bottom:solid 1px black">Hormat Kami</td>
            </tr>
        </table>
    </td>
</tr>
</table>';

    
    
return '<div style="font-style:0.9em">'.$html.'</div>';
}
?>
