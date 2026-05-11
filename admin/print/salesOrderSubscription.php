<?php  

includeClass(array('SalesOrderSubscription.class.php'));
$salesOrderSubscription = new SalesOrderSubscription();


$obj = $salesOrderSubscription;

// $pdf->customSettings(
//    array( 
//         'footer' => '', 
//         'showPrintHeader' => false,
//         ) 
//);  

$generateReportContent = function ($dataset){ 
    
$obj = new SalesOrderSubscription(); 
$customer = new Customer(); 
$employee = new Employee(); 
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsDetailMonthly = $obj->getMonthlyDetailRelatedInformation($rs[0]['pkey']);  
//$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 

$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
$invoiceAddress = '';
$salesMan = '';
$attentionMan = '';
$customerName = '';
$customerPhone = '';
    
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    
$invoiceAddress = $rsCustomer[0]['address'];
$attentionMan = $rsCustomer[0]['attention'];
$customerName = $rsCustomer[0]['name'];
$customerPhone = $rsCustomer[0]['phone'];
$rsSales = $employee->getDataRowById($rsCustomer[0]['saleskey']);
if(!empty($rsSales))
    $salesMan = $rsSales[0]['name'];

$fontWeight = 'bold';
$border= 'border-bottom:solid 1px black;border-top:solid 1px black;';
$profileImg = $obj->loadSetting('companyLogo'); 
//$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=1000&h=500&hash='.getPHPThumbHash($profileImg);

//$arrRecipient = array();
//array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">SALES CONFIRMATION</div></td></tr>
<tr><td><div class="subtitle">No : '.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table cellpadding="4" style="font-size:12px;"> 
<tr>
<td class="header-row-header" style="width: 100px; border:solid 1px black;">Client</td><td style="width:13px;'.$border.'">:</td><td style="width: 233px;'.$border.';border-right:solid 1px black;">'.$customerName.' </td>
<td class="header-row-header" style="width: 100px; border:solid 1px black;">Date</td><td style="width:13px;'.$border.'">:</td><td style="width: 220px;'.$border.';border-right:solid 1px black;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
</tr>
<tr>
<td class="header-row-header" style="width: 100px; border:solid 1px black;">Attention</td><td style="width:13px;'.$border.'">:</td><td style="width: 233px;'.$border.';border-right:solid 1px black;">'.$attentionMan.'</td>
<td class="header-row-header" style="width: 100px; border:solid 1px black;">P.I.C</td><td style="width:13px;'.$border.'">:</td><td style="width: 220px;'.$border.';border-right:solid 1px black;;">'.$rsEmployee[0]['name'].'</td>
</tr>  
<tr>
<td class="header-row-header" style="width: 100px; border:solid 1px black;">Phone Number</td><td style="width:13px;'.$border.'">:</td><td style="width: 233px;'.$border.';border-right:solid 1px black;">'.$rsCustomer[0]['phone'].'</td>
<td class="header-row-header" style="width: 100px; border:solid 1px black;">Product</td><td style="width:13px;'.$border.'">:</td><td style="width: 220px;'.$border.';border-right:solid 1px black;">'.$rs[0]['product'].'</td>
</tr> 
<tr>
<td rowspan="2" class="header-row-header" style="width: 100px; border:solid 1px black;">Address</td><td rowspan="2" style="width:13px;'.$border.'">:</td><td colspan="3" rowspan="2"style="width: 566px;'.$border.';border-right:solid 1px black;   ">'.str_replace(chr(13),'<br>',$invoiceAddress).'</td>
</tr>
<tr>
<td></td>
</tr>
</table> 
<div style="clear:both"></div>';

$html .= ' 
<table cellpadding="4" class="" style="font-size:12px;">
<tr class="" ><td colspan="5" style=" font-weight:'.$fontWeight.'; font-size:14px; width: 680px;text-align:center; border:solid 1px black; background-color:#0f99a4; color:white;">First Month Cost<br></td></tr>
<tr class="" ><td style="border:solid 1px black;width:40px; font-weight:'.$fontWeight.'; text-align:center;">No</td><td style="border:solid 1px black;width:320px; font-weight:'.$fontWeight.';text-align:center;">Description</td><td style="border:solid 1px black;font-weight:'.$fontWeight.'; width:70px;text-align:right" >Quantity</td><td style="border:solid 1px black;width:60px; font-weight:'.$fontWeight.';" >Unit</td><td style="border:solid 1px black;font-weight:'.$fontWeight.';width:80px;text-align:right" >Price (IDR)</td><td style="border:solid 1px black;font-weight:'.$fontWeight.'; text-align:right; border:solid 1px black;width:110px;">Subtotal</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

    $html .= '<tr>
            <td style="border:solid 1px black; text-align:center">'.($i+1).'</td>
            <td style="border:solid 1px black;">'.$rsDetail[$i]['itemname'].'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
            <td style="border:solid 1px black;">'. $rsDetail[$i]['unitname'] .'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
            </tr>' ; 
}
$html .= '</table>' ;
        
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$arrSubtotal = array(); 
    
     
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}    

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="border:solid 1px black; text-align:right; font-weight:bold;">DPP</td><td style="border:solid 1px black; text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="border:solid 1px black; text-align:right; font-weight:bold;">Pajak</td><td style="border:solid 1px black; text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');

}   
     
if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style="border:solid 1px black;text-align:right; font-weight:bold;">Total</td><td style="border:solid 1px black;text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
//$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:490px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td>
<td style="text-align:right; font-weight:bold;  border:solid 1px black; width:80px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold; border:solid 1px black; width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div>';
    
    
$html .= ' 
<table cellpadding="4" class="" style="font-size:12px;">
<tr class="" ><td colspan="5" style=" font-weight:'.$fontWeight.'; font-size:14px; width: 680px;text-align:center; border:solid 1px black; background-color:#0f99a4; color:white;">Monthly Cost<br></td></tr>
<tr class="" ><td style="border:solid 1px black;width:40px; font-weight:'.$fontWeight.'; text-align:center;">No</td><td style="border:solid 1px black;width:320px; font-weight:'.$fontWeight.';text-align:center;">Description</td><td style="border:solid 1px black;font-weight:'.$fontWeight.'; width:70px;text-align:right" >Quantity</td><td style="border:solid 1px black;width:60px; font-weight:'.$fontWeight.';" >Unit</td><td style="border:solid 1px black;font-weight:'.$fontWeight.';width:80px;text-align:right" >Price (IDR)</td><td style="border:solid 1px black;font-weight:'.$fontWeight.'; text-align:right; border:solid 1px black;width:110px;">Subtotal</td></tr>';

for ($i=0;$i<count($rsDetailMonthly);$i++){  

    $html .= '<tr>
            <td style="border:solid 1px black; text-align:center">'.($i+1).'</td>
            <td style="border:solid 1px black;">'.$rsDetailMonthly[$i]['itemname'].'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetailMonthly[$i]['qty']).'</td>
            <td style="border:solid 1px black;">'. $rsDetailMonthly[$i]['unitname'] .'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetailMonthly[$i]['priceinunit']).'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetailMonthly[$i]['total']).'</td>
            </tr>' ; 
}
$html .= '</table>' ;
        
$sayNumberMonthly = $obj->sayNumber($rs[0]['grandtotalmonthly']);
    
$arrSubtotalMonthly = array(); 
    
if ($rs[0]['taxvaluemonthly'] != 0){
    array_push($arrSubtotalMonthly, '<tr><td style="border:solid 1px black;text-align:right; font-weight:bold;">DPP</td><td style="border:solid 1px black;text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotalmonthly']).'</td></tr>');
    array_push($arrSubtotalMonthly, '<tr><td style="border:solid 1px black;text-align:right; font-weight:bold;">Pajak</td><td style="border:solid 1px black;text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvaluemonthly']).'</td></tr>');

}   
     
if ( !empty($arrSubtotalMonthly)){
    array_push($arrSubtotalMonthly, '<tr><td style="border:solid 1px black; text-align:right; font-weight:bold;">Total</td><td style="border:solid 1px black;text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotalmonthly']).'</td></tr>');
}    
 
$subtotalmonthlyLabel = (!empty($arrSubtotalMonthly)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
//$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotalMonthly) + 1).'" style="width:490px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumberMonthly).' Rupiah.</td>
<td style="text-align:right; border:solid 1px black; font-weight:bold;  width:80px;">'.$subtotalmonthlyLabel.'</td>
<td style="text-align:right; border:solid 1px black; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotalmonthly']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotalMonthly); 

$signImg = (!empty($rsSales)) ? HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'employee-photo/'.$rsSales[0]['pkey'].'/'.$rsSales[0]['signaturefile'].'&w=200&h=60&hash='.getPHPThumbHash($rsSales[0]['signaturefile']) : '';
  
$html .= '
</table>
<div style="clear:both"></div>';

$html .= '<table>';
    
$html .= '<tr>
            <td style="height:90px;">Prepared By,</td>
            <td>Approved By,</td>
          </tr> 
          <tr>
            <td style="height:90px;">'.$signImg.'</td>
            <td></td>
          </tr> 
          <tr>
            <td style="font-weight: '.$fontWeight.'; font-size:14px;">'.ucwords($salesMan).'</td>
            <td style="font-weight: '.$fontWeight.'; font-size:14px;">'.ucwords($attentionMan).'</td>
          </tr>
          <tr>
            <td style="font-weight: '.$fontWeight.'; font-size:14px;">PT Mitra Visioner Pratama</td>
            <td style="width:380px;font-weight: '.$fontWeight.'; font-size:14px;">'.ucwords($customerName).'</td>
          </tr>';
$html .= '</table><div style="clear:both"></div>';
    
$html .= '<table cellpadding="4">';
    
$html .= '<tr><td style="  font-style: italic;">Terms and Conditions</td></tr>
          <tr> <td style="  font-style: italic;">1. All Equipment is provided by MVP</td></tr>
          <tr> <td style="  font-style: italic;">2. There wont be any further action before SC approval</td></tr>
          <tr>  <td style="  font-style: italic;">3. Kindly please return this SC 7(seven) days after the SC date at max</td></tr>';

$html .= '</table>';
    
//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}


?>
