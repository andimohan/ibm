<?php  

includeClass(array('InvoiceOrderSubscription.class.php'));
$invoiceOrderSubscription = createObjAndAddToCol(new InvoiceOrderSubscription()); 

$obj = $invoiceOrderSubscription;

$generateReportContent = function ($dataset){ 

$obj = new InvoiceOrderSubscription(); 
$salesOrderSubscription = new SalesOrderSubscription(); 
$customer = new Customer(); 
$employee = new Employee(); 
$termOfPayment = new TermOfPayment();
$setting = new Setting();

$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);  
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$duedate = date('d-m-Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));
$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'].' Days' : $obj->lang['cash'];

$periode = date('F',strtotime('-1 day', strtotime($rs[0]['trdate'])));    
    
$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
$invoiceAddress = '';
$salesMan = '';
$attentionMan = '';
$customerID = '';
$customerName = '';
$customerPhone = '';
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$arrCustomer = array();
//if($rsCustomer[0]['ismainaccount']){
    $invoiceAddress = $rsCustomer[0]['address'];
    $attentionMan = $rsCustomer[0]['attention'];
    $customerName ='<strong>'.$rsCustomer[0]['name'].'</strong>';
    $customerID = $rsCustomer[0]['code'];
    $customerPhone = $rsCustomer[0]['phone'];
    
    array_push($arrCustomer,$customerName,str_replace(chr(13),'<br>',$invoiceAddress),$customerPhone);

    
/*}else{
    $rsCustomerHO = $customer->searchData($customer->tableName.'.pkey',$rsCustomer[0]['parentkey'],true); 
    $invoiceAddress = $rsCustomerHO[0]['address'];
    $attentionMan = $rsCustomerHO[0]['attention'];
    $customerName ='<strong>'.$rsCustomerHO[0]['name'].'</strong>';
        $customerID = $rsCustomerHO[0]['code'];

    $customerPhone = $rsCustomerHO[0]['phone'];
    $rsSales = $employee->getDataRowById($rsCustomerHO[0]['saleskey']);
    if(!empty($rsSales))
        $salesMan = $rsSales[0]['name'];
    
    array_push($arrCustomer,$customerName,str_replace(chr(13),'<br>',$invoiceAddress),$customerPhone);

}*/

$fontWeight = 'bold';
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyEmail = $setting->getDetailByCode('companyEmail');
$arrCompanyPhone = array();  

for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyEmail = array();  
for($i=0;$i<count($companyEmail);$i++) 
    array_push($arrCompanyEmail, $companyEmail[$i]['value']);

$companyMail = '';
if(!empty($arrCompanyEmail))
    $companyMail = implode (', ', $arrCompanyEmail);
    
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=260&h=130&hash='.getPHPThumbHash($profileImg);

//$arrRecipient = array();
//array_push($arrRecipient, $rs[0]['recipientname'], str_replace(chr(13),'<br>',$rs[0]['recipientaddress']), $rs[0]['recipientphone']);
    
$html = $obj->printSetting['defaultStyle'];
//$html .= ' 
//<table cellpadding="2" > 
//<tr><td><div class="title">INVOICE</div></td></tr>
//<tr><td><div class="subtitle">No : '.$rs[0]['code'].'</div></td></tr>
//</table>'; 
$html .= '<table style="">
<tr>
<td rowspan="4" style="width:260px;"><table><tr><td style="vertical-align:middle;width:200px;"><img src="'.$img.'"></td></tr></table>

</td>
<td style="width:260px;"></td>
<td style="width:180px;text-align:center;font-size:28px;font-weight:bold;">INVOICE</td>
</tr>
</table>
';
$html .='

<div style="clear:both"></div>
<table cellpadding=""> 
<tr>
<td style="width:260px;"></td>
<td style="width:310px;text-align:right;">DATE</td>
<td style="width:110px;border:solid 1px black;text-align:center;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
</tr>
<tr>
<td  ><table cellpadding="" style="color:#0f99a4;"><tr><td style="width:20px;"></td><td style="width:90px;">Phone</td><td style="width:200px;">'.$companyContact.'</td></tr></table>
</td>
<td style="text-align:right;">INVOICE#</td>
<td style="border:solid 1px black;text-align:center;">'.$rs[0]['code'].'</td>
</tr>
<tr>
<td  ><table cellpadding="" style="color:#0f99a4;"><tr><td style="width:20px;"></td><td style="width:90px;">Email</td><td style="width:200px;">'.$companyMail.'</td></tr></table>
</td>
<td style="text-align:right;">CUSTOMER ID</td>
<td style="border:solid 1px black;text-align:center;">'.$customerID.'</td>
</tr>
<tr>
<td  ><table cellpadding="" style="color:#0f99a4;"><tr><td style="width:20px;"></td><td style="width:90px;">www.mvnet.co.id</td><td style="width:200px;"></td></tr></table>
</td>
<td style="text-align:right;">DUE DATE</td>
<td style="border:solid 1px black;text-align:center;">'.$topSaid.'</td>
</tr>
<tr>
<td style="background-color:#202c8c;color:white;font-weight:'.$fontWeight.'">BILL TO</td>
<td style="text-align:right;">PERIODE</td>
<td style="border:solid 1px black;text-align:center;">'.$periode.'</td>
</tr>
<tr>
<td >'.implode('<br>',$arrCustomer).'</td>
<td ></td>
<td ></td>
</tr>  
</table> 
<div style="clear:both"></div>';

$html .= ' 
<table cellpadding="4" class="" style="border:solid 1px black;font-size:12px;">
<tr class="" >
<td style="border:solid 1px black;width:490px; font-weight:'.$fontWeight.';background-color:#202c8c;color:white;">Description</td>
<td style="border:solid 1px black;width:80px; font-weight:'.$fontWeight.';background-color:#202c8c;color:white;text-align:center" >Unit</td>
<td style="background-color:#202c8c;color:white;border:solid 1px black;font-weight:'.$fontWeight.';width:110px;text-align:center" >AMOUNT</td>
</tr>';

for ($i=0;$i<count($rsDetail);$i++){  

    $html .= '<tr>
            <td style="border-right:solid 1px black;">'.$rsDetail[$i]['itemname'].'</td>
            <td style="border-right:solid 1px black;text-align:center">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
            <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
            </tr>' ; 
}
$html .= '</table>' ;
        
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$arrSubtotal = array(); 
    
     
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td style=" font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}    

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style=" font-weight:bold;">DPP</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style=" font-weight:bold;">Pajak</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');

}   
     
if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style=" font-weight:bold;border-top:solid 1px black">Grand Total</td><td style="text-align:right; font-weight:bold;border-top:solid 1px black"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
//$topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
    
$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:490px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td>
<td style=" font-weight:bold;  width:80px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
<td colspan="2"></td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div><div style="clear:both"></div>';
    
$html .= '<table cellpadding="4" style="border:solid 1px black;width:450px;">';
    
$html .= '<tr><td style="font-weight:bold;background-color:#202c8c;color:white;border:solid 1px black;">OTHER COMMENTS</td></tr>
          <tr> <td style=" ">1. Total payment due in '.$topSaid.'</td></tr>
          <tr> <td style="">2. Please include the invoice number on your check</td></tr>
          <tr>  <td style=" "></td></tr>
          <tr>  <td style=" "></td></tr>
          <tr>  <td style=" "></td></tr>
          ';

$html .= '</table><div style="clear:both"></div><div style="clear:both"></div>';
    
$html .= '<table><tr><td style="font-size:12px;font-style:italic; text-align:center;">Thank You for Your Business!</td></tr></table>';
    
//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}


?>
