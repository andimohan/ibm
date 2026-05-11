<?php 

  

$arrSignLabel = array(); 
array_push($arrSignLabel, array($obj->lang['created'],$user));
array_push($arrSignLabel, array($obj->lang['messenger']));
array_push($arrSignLabel, array($obj->lang['received']));

$signTable =' 
    <table cellpadding="4" class="sign">
    <tr>'; 
    for ($i=0;$i<count($arrSignLabel);$i++){
        $signTable .='<td  class="sign-col" style="height:70px;border-bottom:1px solid black;"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
        if ($i <> count($arrSignLabel) - 1)
            $signTable .= '<td class="sign-col-space"></td>';
    }
    $signTable .='</tr>  ';
    $signTable .='<tr><td> </td><td></td> </tr>';
    $signTable .='<tr><td> </td> <td></td></tr>';
    $signTable .='<tr><td> </td> <td></td></tr></table>';

$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false, 
		 'marginFooter' => '25',
         'footer' => $signTable
         ) 
);


$generateReportContent = function ($dataset){ 
 
$obj = new EMKLInvoiceReceipt();  
$emklOrderInvoice = new EMKLOrderInvoice();
$customer = new Customer();
$employee = new Employee();
  
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey'] );  
$customerName = (!empty($rsCustomer)) ? $rsCustomer[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 

        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle; width:180px;font-size:2.4em;font-weight:bold;font-family:Arial Black;font-style:italic" >OKATRANS</td>
            </tr>
        </table>
        
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['invoiceReceipt'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['customer'].'</td><td style="text-align:center">:</td><td>'. $customerName .'</td></tr>
</table>
</td>
<td style="width:370px;"> 
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['number'],'width' => '30'));
array_push($cellArray, array('label' => $obj->lang['invoiceCode']));
//array_push($cellArray, array('label' => $obj->lang['note'])); 
array_push($cellArray, array('label' => $obj->lang['invoiceDate'], 'width' => '130','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['curr'], 'width' => '40','align' =>'center')); 
array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right', 'width' => '100'));  
array_push($cellArray, array('label' => $obj->lang['downpayment'],'align' =>'right', 'width' => '100'));  
array_push($cellArray, array('label' => $obj->lang['total'],'align' =>'right', 'width' => '100'));  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  

    
for($i=0;$i<count($rsDetail);$i++){ 
    //$rsInvoice = $emklOrderInvoice->getDataRowById($rsDetail[$i]['invoicekey']);
    $html .= '<tr><td style="text-align:right">'.($i+1).'.</td><td>'.$rsDetail[$i]['invoicecode'].'</td><td style ="text-align:center">'.$obj->formatDBDate($rsDetail[$i]['invoicedate']).'</td><td style ="text-align:center">'. $rsDetail[$i]['currencyname'].'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['totaldownpayment']).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']-$rsDetail[$i]['totaldownpayment']).'</td></tr>';
  }  

//
//<div style="clear:both"></div> 
//<table cellpadding="4"> 
//<tr><td rowspan="3" style="width:440px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
//</table>
//<div style="clear:both"></div>   
    
    
$html .= '</table>
'.$trnotes.'
<div style="clear:both"></div>  
';
 
return '<div style="font-size:12px;">'.$html.'</div>';
}

?>
