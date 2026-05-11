<?php 
$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'pdfMarginBody' => 28,
         'footer' => '', 
         'showPrintHeader' => false,
         ) 
); 

$obj = $purchaseOrder;
$generateReportContent = function ($dataset){ 
$obj = new PurchaseOrder(); 
$supplier = new Supplier();
$termOfPayment = new TermOfPayment();
$city = new City();
$setting = new Setting(); 
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);   
    
$arrRecipient = array();
      
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
    
    
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($obj->loadSetting('companyName'));
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= '<div style="clear:both"></div>';
$html .= '<table>
    <tr>
        <td style="width:375px">
        <table cellpadding="2"> 
            <tr><td class="header-row-header" colspan = "2" style="width:375px">'.$companyName.'<br>'.$companyContact.'</td></tr>
            <tr><td colspan = "2"></td></tr>
            <tr><td class="header-row-header" style="width:70px;">No. Faktur</td><td style="width:10">:</td><td>'.$rs[0]['code'].'</td></tr> 
        </table>
        </td>
        <td style="width:300px"><table cellpadding="2"> 
            <tr><td class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10">:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
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
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Ongkos Kirim</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['shipmentfee']).'</td></tr>');
   
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


return '<div style="font-size:0.9em">'.$html.'</div>';
}
?>
