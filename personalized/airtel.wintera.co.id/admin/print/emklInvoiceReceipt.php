<?php 
$pdf->setCustomSettings(
    array( 
         'showPrintHeader' => false,
         'footer' => '',  
         ) 
);
 
$generateReportContent = function ($dataset){ 
 global $pdf;

$obj = new EMKLInvoiceReceipt();  
$emklOrderInvoice = new EMKLOrderInvoice();
$customer = new Customer();
$employee = new Employee();
  
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailById($rs[0]['pkey']); 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey'] );  
$customerName = (!empty($rsCustomer)) ? $rsCustomer[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$draft = ($rs[0]['statuskey'] == 1) ? ' / DRAFT' : '';

$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['invoiceReceipt']).' '.$draft.'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header" >'.$obj->lang['customer'].'</td><td style="text-align:center">:</td><td style="width:350px">'. $customerName .'</td></tr>
</table>
</td>
<td style="width:370px;"> 
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
array_push($cellArray, array('label' => $obj->lang['invoiceNumber'], 'width' => '130'));
array_push($cellArray, array('label' => $obj->lang['invoiceDate'], 'width' => '130','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['note'])); 

//array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right', 'width' => '110'));  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  
 


for($i=0;$i<count($rsDetail);$i++){ 
    $rsInvoice = $emklOrderInvoice->getDataRowById($rsDetail[$i]['invoicekey']);
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td>'.$rsInvoice[0]['code'].'</td><td style ="text-align:center">'.$obj->formatDBDate($rsInvoice[0]['trdate']).'</td><td>'.$rsDetail[$i]['description'].'</td></tr>';
}  

    
$html .= '    
</table>  

';

//    $html .= '<div style="clear:both"></div> 
//<table cellpadding="4"> 
//<tr><td rowspan="3" style="width:440px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
//</table>
//';
    
$html .= '<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
';
  
$arrSignedUser = array();
if (!empty($rs[0]['createdby'])){ 
    $rsEmployee = $employee->getDataRowById($rs[0]['createdby']);
    $user = $rsEmployee[0]['name'];
}
array_push($arrSignedUser, array($obj->lang['invoice'].' / '.$obj->lang['messenger'], $user));     
array_push($arrSignedUser, array($obj->lang['received']));    
$html .= $obj->generateSignLabel($rs, $arrSignedUser); 
return $html;
}

?>