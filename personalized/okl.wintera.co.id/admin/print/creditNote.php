<?php 
$pdf->setCustomSettings(
    array(
        'showPrintHeader' => false,
        'showPrintFooter' => false,
        'footer' => '',
    )
);

includeClass(array('CreditNote.class.php'));
$creditNote = createObjAndAddToCol(new CreditNote()); 
 
$obj = $creditNote;
 
$generateReportContent = function ($dataset){ 
 
$obj = new CreditNote();  
$emklOderInvoice = new EMKLOrderInvoice();
$customer = new Customer();
$employee = new Employee();
    
$rs = $dataset['rs'];
  
$decimalNumber = ($rs[0]['currencykey'] == CURRENCY['idr']) ? 0 : 2;
$currencySay = ($rs[0]['currencykey'] == CURRENCY['idr']) ?'Rupiah' : 'USD';
    
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey'] );  
$customerName = (!empty($rsCustomer)) ? $rsCustomer[0]['name'] : '';
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<div></div>
<div></div>
<div></div>
<div></div>
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['creditNote'].'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td class="header-row-header" >'.$obj->lang['customer'].'</td><td style="text-align:center">:</td><td style="width:300px">'. $customerName .'</td></tr>
<tr><td class="header-row-header" >'.$obj->lang['currency'].'</td><td style="text-align:center">:</td><td style="width:300px">'. $rs[0]['currencyname'] .'</td></tr>
</table>
</td>
<td style="width:370px;"> 
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';

$cellArray = array (); 
array_push($cellArray, array('label' => $obj->lang['arCode'], 'width' => '150' ));
array_push($cellArray, array('label' => $obj->lang['invoiceCode'], 'width' => '130'));  
array_push($cellArray, array('label' => $obj->lang['date'], 'width' => '130','align' =>'center'));  
array_push($cellArray, array('label' => $obj->lang['amount'],'align' =>'right'));  
array_push($cellArray, array('label' => $obj->lang['creditNote'],'width' => '130','align' =>'right'));  
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','cell' =>  $cellArray));  

for($i=0;$i<count($rsDetail);$i++){ 
//    $rsAr = $ar->getDataRowById($rsDetail[$i]['arkey']);
    $html .= '<tr><td>'.$rsDetail[$i]['arcode'].'</td><td>'.$rsDetail[$i]['refcode'].'</td><td style ="text-align:center">'.$obj->formatDBDate($rsDetail[0]['ardate'],'',array('returnOnEmpty' => true, 'value' => '')).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['artotal'],$decimalNumber).'</td><td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['totalcredit'],$decimalNumber).'</td></tr>';
}  

    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td style="width:440px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' '.$currencySay.'.</td><td style="text-align:right; font-weight:bold;  width:130px; ">Total</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal'],$decimalNumber).'</td></tr>
</table>
<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
';

      $html .= '<table cellpadding="2">
       
        <tr> 
            <td style="width:550px;"></td>
            <td style="width:125px;">
            <table>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td><b>Suprihanto</b></td></tr>
                </table>
            </td>
        </tr>
    </table>';
    
return $html;
}

?>
