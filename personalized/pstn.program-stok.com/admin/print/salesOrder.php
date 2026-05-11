<?php 
$pdf->setCustomSettings(
    array(   
         'footer' => '', 
         'showPrintHeader' => false,
         ) 
); 

$obj = $salesOrder;
$generateReportContent = function ($dataset){ 
$obj = new SalesOrder(); 
$setting = new Setting(); 
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],'',' order by brandname asc, itemcategoryname asc');   
    
$arrRecipient = array();

	
//if(!empty($rs[0]['recipientname'])){
//     array_push($arrRecipient, $rs[0]['recipientname']);
//     if (!empty($rs[0]['recipientaddress'])) array_push($arrRecipient, str_replace(chr(13),'<br>',$rs[0]['recipientaddress'])); 
//     if (!empty($rs[0]['recipientphone'])) array_push($arrRecipient, $rs[0]['recipientphone']); 
//}else{
    $customer = new Customer();    
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    array_push($arrRecipient, $rsCustomer[0]['name']);
     //if (!empty($rsCustomer[0]['address'])) array_push($arrRecipient, str_replace(chr(13),'<br>',$rsCustomer[0]['address'])); 
     if (!empty($rsCustomer[0]['phone'])) array_push($arrRecipient, $rsCustomer[0]['phone']); 
     if (!empty($rsCustomer[0]['mobile'])) array_push($arrRecipient, $rsCustomer[0]['mobile']); 
     //if (!empty($rsCustomer[0]['taxid'])) array_push($arrRecipient, 'NPWP :'. $rsCustomer[0]['taxid']); 
     //if (!empty($rsCustomer[0]['taxregistrationaddress'])) array_push($arrRecipient, 'Alamat :'. $rsCustomer[0]['taxregistrationaddress']); 
//}
	
$groupSubtotal = (in_array( $rs[0]['customerkey'],array(15))) ? true : false;
    
// 0: normal,  1: SJ2 
$invoiceType = (isset($_GET) && !empty($_GET['invoiceType'])) ?  $_GET['invoiceType'] : 0; 

if($invoiceType == 1){ 
	$header = '
	<table>
		<tr>
			<td>
			<table cellpadding="2"> 
				<tr><td colspan="3"><b>SUMBER JAYA 2</b></td></tr> 
				<tr><td class="header-row-header" style="width:70px;">No. Faktur</td><td style="width:10">:</td><td>'.$rs[0]['code'].'</td></tr> 
			</table>
			</td>
			<td>
				<table cellpadding="2"> 
				<tr><td style="width:80px" class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10">:</td><td style="width:240px" >'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
				<tr><td><strong>Kepada Yth.</strong></td><td>:</td><td>'.implode('<br>',$arrRecipient).'</td></tr> 
				<tr><td><strong>NPWP</strong></td><td>:</td><td>'.$rsCustomer[0]['taxid'] .'</td></tr> 
				<tr><td><strong>Alamat</strong></td><td>:</td><td>'. $rsCustomer[0]['taxregistrationaddress'] .'</td></tr> 
				</table>  
			</td>
		</tr> 
	</table>
	<div style="clear:both;"></div>
	';	
}else{
	$header = '
	<table>
		<tr>
			<td>
			<table cellpadding="2"> 
				<tr><td class="header-row-header" colspan = "2" style="width:300px">CV. Marco Polo<br>
	NPWP 82.192.852.0-072.000<br>
	Tanah Abang Blok B, B1/ E/151-152<br>
	HP : 0816-700-623</td></tr>
				<tr><td colspan = "2"></td></tr>
				<tr><td class="header-row-header" style="width:70px;">No. Faktur</td><td style="width:10">:</td><td>'.$rs[0]['code'].'</td></tr> 
			</table>
			</td>
			<td>
				<table cellpadding="2"> 
				<tr><td style="width:80px" class="header-row-header">'.ucwords($obj->lang['date']).'</td><td style="width:10">:</td><td style="width:240px" >'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>   
				<tr><td><strong>Kepada Yth.</strong></td><td>:</td><td>'.implode('<br>',$arrRecipient).'</td></tr> 
				<tr><td><strong>NPWP</strong></td><td>:</td><td>'.$rsCustomer[0]['taxid'] .'</td></tr> 
				<tr><td><strong>Alamat</strong></td><td>:</td><td>'. $rsCustomer[0]['taxregistrationaddress'] .'</td></tr> 
				</table>  
			</td>
		</tr> 
	</table>
	<div style="clear:both;"></div>
	';
}

    
$companyPhone = $setting->getDetailByCode('companyPhone');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($obj->loadSetting('companyName'));
     
    
$html = $obj->printSetting['defaultStyle'];

if($rs[0]['statuskey'] == 1) $html .= '<div style="text-align:center; font-weight: bold; font-size: 1.2em">INVOICE<br></div>';
else if($rs[0]['statuskey'] == 2 || $rs[0]['statuskey'] == 3)   $html .= '<div style="text-align:center; font-weight: bold; font-size: 1.2em">PACKING LIST<br></div>';  

	$html .= $header;

$cellArray = array();
array_push($cellArray, array('label' => 'No.', 'width' => '40','align' => 'right'));
array_push($cellArray, array('label' => 'Jumlah', 'width' => '60','align' => 'right'));
array_push($cellArray, array('label' => 'Nama Barang'));
array_push($cellArray, array('label' => 'harga @','align' => 'right', 'width' => '60')); 
array_push($cellArray, array('label' => 'Subtotal','align' => 'right', 'width' => '100'));
  
$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));

$tempBrandKey = 0;
$tempCategoryKey = 0;
$tempTotal = 0;
$tempPrice = 0;
$totalQty = 0;
$unitName = '';
    
$arrTotalQty = array();
for ($i=0;$i<count($rsDetail);$i++){  
    $itemCategoryKey = $rsDetail[$i]['itemcategorykey'];
    $itemCategoryName = $rsDetail[$i]['itemcategoryname'];
    $itemUnit = $rsDetail[$i]['unitname'];
    $brandKey = $rsDetail[$i]['brandkey'];
    $unitName = $rsDetail[$i]['unitname'];
      
    if($groupSubtotal){ 
         if($tempBrandKey <> $brandKey || $tempCategoryKey <> $itemCategoryKey){

            if ($i > 0)
                $html .= '<tr><td></td><td style="text-align:right; border-top:1px solid #666;">'.$obj->formatNumber($tempTotal).' '. $unitName.'<br></td><td colspan="2"></td><td style="text-align:right;border-top:1px solid #666;">'.$obj->formatNumber($tempPrice).'</td></tr>' ; 

            $tempTotal = 0;   
            $tempPrice = 0;   
        } 
    }
    
    
    if(!isset($arrTotalQty[$itemCategoryKey])) $arrTotalQty[$itemCategoryKey] = array('name' => $itemCategoryName,'qty' => 0, 'unit' => $itemUnit);
        
    
    $tempTotal += $rsDetail[$i]['qty'];
    $tempPrice += $rsDetail[$i]['total'];
    $totalQty += $rsDetail[$i]['qty'];
    $arrTotalQty[$itemCategoryKey]['qty'] += $rsDetail[$i]['qty'];
 
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).' '. $rsDetail[$i]['unitname'] .'</td><td>'.$rsDetail[$i]['itemname'].'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 
     
    $tempBrandKey = $brandKey;
    $tempCategoryKey = $itemCategoryKey; 
     
}    
    
if($groupSubtotal)
    $html .= '<tr><td></td><td style="text-align:right; border-top:1px solid #666;">'.$obj->formatNumber($tempTotal).' '.$unitName .'<br></td><td colspan="2"></td><td style="text-align:right;border-top:1px solid #666;">'.$obj->formatNumber($tempPrice).'</td></tr>' ; 
   
    
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
/*    
if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">DPP</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
}   */
    
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
   
$footer = $obj->loadSetting('invoiceFooter');
if(!empty($footer))
    $footer = '<br><br>'.$footer;
    
$decription = (!empty($rs[0]['trdesc'])) ? '<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
    
$html .= '<table cellpadding="2" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:475px">';

$html  .= '<b>Total: '.$obj->formatNumber($totalQty).' ' .$unitName.'</b><br>';
    

if($groupSubtotal){ 
    $html .= '<table>';
    foreach($arrTotalQty as $totalQty)  
        $html .= '<tr><td style="width:120px">'.$totalQty['name'].'</td><td>: '.$obj->formatNumber($totalQty['qty']).' '. $totalQty['unit'].'</td></tr>'; 
    $html .= '</table>'; 
}

$html .= $decription.$footer.' 
</td>
<td style="text-align:right; font-weight:bold;  width:90px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
if($invoiceType == 1){ 
$ppnInclude = '';
}else{
$ppnInclude = '<table cellpadding="0">
 <tr><td>Transfer ke : Bank BCA 2538888678<br>Atas Nama : CV. MARCO POLO</td><td style="text-align:right;">Harga diatas sudah termasuk PPN</td></tr>
 </table>'; 
}
	
$html .= '
</table>'.$ppnInclude.' 
<div style="clear:both"></div>
';
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Hormat Kami'));
array_push($arrSignLabel, array('Telah Diterima'));

$html .=' 
<br><br>
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