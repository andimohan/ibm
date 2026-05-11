<?php   
$pdf->setCustomSettings(
    array( 
            'showPrintHeader' => false, 
            'pdfMarginHeader' => '10', 
            'footer' => '',
         ) 
);  
 

$generateReportContent = function ($dataset){ 
    
global $pdf;
$obj = new SalesOrder(); 
$termOfPayment = new TermOfPayment();
$setting = new Setting();
$employee = new Employee();

$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($rs[0]['pkey']);    
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (',', $arrCompanyPhone);
    
    
$confirmedName = '';
if (!empty($rs[0]['confirmedby'])){ 
    $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby']);
    $confirmedName = $rsEmployee[0]['name'];
}    

$companyName = strtoupper($setting->loadSetting('companyName'));
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';

$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] : $obj->lang['cash'];
$profileImg = $obj->loadSetting('companyLogo'); 
//$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=160&h=70&hash='.getPHPThumbHash($profileImg);

    
$arrRecipient = array();
array_push($arrRecipient, '<b>'.$rs[0]['recipientname'].'</b>', str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$imgSignature = '';// HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=140&h=70&hash='.getPHPThumbHash($profileImg);    
    
$html = $obj->printSetting['defaultStyle'];
$html .= '<table >
    <tr>
        <td style="width:450px">
       
        </td> 
        <td>
            <table cellpadding="2" style="">    
            <tr><td style="width:70px"></td><td style="font-size:2.5em;text-align:right;width:150px">Invoice</td></tr>   
            <tr><td style="width:70px"></td><td  style="text-align:right;width:150px">'.$rs[0]['code'].'</td></tr>   
            </table>  
        </td>
    </tr> 
</table>
<div style="clear:both;"></div><div style="clear:both;"></div>';
        
    
$html .= ' 
<table>
<tr>
<td style="width:380px">
<table cellpadding="2" > 
<tr><td colspan="3" >Billed To :</td><td></td></tr> 
<tr><td colspan="3" style="width: 250px;">'.implode('<br>',$arrRecipient).'</td></tr>

</table> 
</td>
<td>
<table cellpadding="2" style="background-color:#eee"> 
<tr><td style="width:150px">Invoice Date</td><td style="width:20px">:</td><td style="width:120px;text-align:right;">'.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'</td></tr> 
<tr><td style="width:150px">Term of Payment (days)</td><td style="width:20px;">:</td><td style="width:120px;text-align:right;">'.$topSaid.'</td></tr>  

</table> 
</td>
</tr>
</table>
<div style="clear:both;"></div>
';

$html .= '<table  cellpadding="6" class="table-transaction">';

	
$cellArray = array ();
array_push($cellArray, array('label' => 'Services')); 
array_push($cellArray, array('label' => 'Unit', 'align' => 'center', 'width' => '60'));
array_push($cellArray, array('label' => 'Unit Price', 'align' => 'right', 'width' => '100'));
array_push($cellArray, array('label' => 'Amount', 'align' => 'right', 'width' => '100'));
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

for ($i=0;$i<count($rsDetail);$i++){  
  $detailDesc = (!empty($rsDetail[$i]['trdesc'])) ? '<br><i>'.str_replace(chr(13),'<br>',$rsDetail[$i]['trdesc']).'</i>' : '';
  $html .= '<tr><td style="">'.$rsDetail[$i]['itemname'].''.$detailDesc.'</td><td style="text-align:center;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td><td style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
}
$html .= '</table>' ;

//$html .= '<div style="clear:both"></div>';
        
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
    
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '<table cellpadding="6" style=""> 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
	
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div><div style="clear:both"></div>';
    
$html .= '
<table>
<tr>
<td style="width:300px">
<table cellpadding="2">
<tr><td style="width:150px">Payment via transfer to :</td><td></td><td></td></tr>
<tr><td style="width:150px"></td><td></td><td></td></tr>
<tr><td style="width:100px">Account Name</td><td style="width:10px">:</td><td style="width:200px">Martin Halim Kusuma</td></tr>
<tr><td style="width:100px">Bank</td><td style="width:10px">:</td><td style="width:180px">Bank Central Asia</td></tr>
<tr><td style="width:100px">Bank Account</td><td style="width:10px">:</td><td style="width:180px">002 002 6499</td></tr>
</table>
</td> 
<td style="width:400px">
<table> 
<tr>
<td></td>
<td style="height:30px;text-align:center;">Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'</td></tr>
<tr><td></td><td style="height:80px;text-align:center;"></td></tr>
<tr>
<td></td>
<td style="text-align:center;">'.$confirmedName.'</td>
</tr>
</table>
</td>
</tr>
</table>
';

//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
