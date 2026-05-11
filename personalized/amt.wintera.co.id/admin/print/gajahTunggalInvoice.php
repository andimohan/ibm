<?php  
$pdf->setCustomSettings(
    array( 
         'footer' => '',   
         ) 
);  

$invoiceContent = function ($dataset){ 
 
$obj = new TruckingServiceOrderInvoice();  
$truckingServiceOrder = new TruckingServiceOrder();    
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$customer = new Customer();
//$consignee = new Consignee();
$cost = new Service(TRUCKING_SERVICE,1);
$customCode = new CustomCode();
$termOfPayment = new TermOfPayment();
$employee = new Employee();
    
$rs = $dataset['rs']; 
        
$rsDetail = $obj->getDetailById($rs[0]['pkey']);
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsTOP =   $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$duedate = date('d-m-Y', strtotime('+'.$rsTOP[0]['duedays'].' days', strtotime($rs[0]['trdate'])));
 
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table>
<tr>
<td style="width:340px;">
<table cellpadding="2"> 
<tr><td><b>'. $rsCustomer[0]['name'] .'</b><br>'.str_replace(chr(13),'<br>',$rsCustomer[0]['address']).'</td></tr>   
</table> 
</td>
<td style="width:90px;"></td>
<td style="width:330px;">
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:110px">Invoice No.</td><td style="width:10px; text-align:center">:</td><td style="width:110px;">'.$rs[0]['code'].'</td></tr>
<tr><td class="header-row-header" style="width:110px">Date</td><td>:</td><td>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
</table> 
</td>
</tr>
</table>';
      
$html .='<div style="clear:both"></div>
<table cellpadding="4" border="1">
<tr><td style="text-align:left;width:30px"></td>
<td style="text-align:left;width:400px">

<table>
<tr><td style="width:80px">Vessel.</td><td style="width:10px; text-align:center">:</td><td style="width:310px;"></td></tr>
<tr><td style="width:80px">From</td><td style="text-align:center">:</td><td></td></tr>
<tr><td style="width:80px"></td><td style="text-align:center"></td><td></td></tr>

</table>
</td>
<td style="text-align:center;width:120px">Job Number</td><td style="text-align:center;width:120px">Job Date</td></tr>
</table>
<table cellpadding="4" class="table-transaction" border="1">
<tr class="col-header"><td style="text-align:center;width:30px">NO</td><td style="text-align:center;width:400px; ">DESCRIPTION</td><td style="width:120px;"></td><td style="text-align:center;width:120px;">IDR</td></tr>  
<tr><td></td><td>Being Payment For : '.$rs[0]['trdesc'].'</td><td style="text-align:center;"></td><td></td></tr>
';
        
    
$color = '#333';
     
for($i=0;$i<count($rsDetail);$i++){ 
    
    $description = $rsDetail[$i]['description']; 
    $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);  
    
    $html .= '<tr><td style="text-align:right; font-weight:bold ">'.($i+1).'.</td><td>SI NO  '.$rsSOHeader[0]['donumber'].'</td><td style="text-align:center;">'.$rsSOHeader[0]['code'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>';
    

} 
$arrSubtotal = array();
$ctr = 1;   
     
if ($rs[0]['finaldiscount'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['discount']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
    $ctr += 1;
}

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['beforeTax']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['PPN']).'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');
    $ctr += 2;
}
    
if ($rs[0]['totaldownpayment'] > 0){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">'.ucwords($obj->lang['downpayment']).'</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['totaldownpayment']).'</td></tr>'); 
    $ctr += 1;
} 

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['outstanding']);
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.$ctr.'" style="width:410px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td> <td style="text-align:right; font-weight:bold;  width:150px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['subtotal']).'</td></tr>
';

$html .= implode('',$arrSubtotal);
    
if (!empty($arrSubtotal))  
$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['outstanding']).'</td></tr>';

    
if ($rs[0]['tax23value'] != 0)  { 
//$html .= '<tr><td colspan="3"></td></tr>';
$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">PPH 23</td><td style="text-align:right; font-weight:bold;"  >('.$obj->formatNumber($rs[0]['tax23value']).')</td></tr>';
$html .= '<tr><td></td> <td style="text-align:right; font-weight:bold;  ">Yang Harus Dibayarkan</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber(abs($rs[0]['grandtotal']-$rs[0]['tax23value'])).'</td></tr>';
}
 
 
return $html;
}; 
   
$generateReportContent = array();
array_push($generateReportContent , $invoiceContent);

?>